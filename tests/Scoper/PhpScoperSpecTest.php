<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\Scoper;

use function array_diff;
use function array_keys;
use function array_map;
use function current;
use Generator;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use function is_array;
use LogicException;
use const PHP_EOL;
use PhpParser\Error as PhpParserError;
use PhpParser\Node\Name;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;
use Symfony\Component\Finder\Finder;
use Throwable;
use UnexpectedValueException;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_parser;
use function implode;
use function sprintf;

class PhpScoperSpecTest extends TestCase
{
    private const SPECS_PATH = __DIR__.'/../../specs';
    private const SECONDARY_SPECS_PATH = __DIR__.'/../../_specs';

    private const SPECS_META_KEYS = [
        'title',
        'prefix',
        'whitelist',
        'whitelist-global-constants',
        'whitelist-global-functions',
        'registered-classes',
        'registered-functions',
    ];

    private const SPECS_SPEC_KEYS = [
        'prefix',
        'whitelist',
        'whitelist-global-constants',
        'whitelist-global-functions',
        'registered-classes',
        'registered-functions',
        'payload',
    ];

    /**
     * This test is to ensure no file is left in _specs for the CI. It is fine otherwise for this test to fail locally
     * when developing something.
     */
    public function test_it_uses_the_right_specs_directory()
    {
        $files = (new Finder())->files()->in(self::SECONDARY_SPECS_PATH);

        $this->assertCount(0, $files);
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(
        string $spec,
        string $contents,
        string $prefix,
        Whitelist $whitelist,
        ?string $expected,
        array $expectedRegisteredClasses,
        array $expectedRegisteredFunctions
    ) {
        $filePath = 'file.php';
        $patchers = [create_fake_patcher()];
        $scoper = $this->createScoper($contents);

        try {
            $actual = $scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

            if (null === $expected) {
                $this->fail('Expected exception to be thrown.');
            }
        } catch (UnexpectedValueException $exception) {
            if (null !== $expected) {
                throw $exception;
            }

            $this->assertTrue(true);

            return;
        } catch (Throwable $throwable) {
            $this->fail('Could not parse the spec: '.$spec);
        }

        $specMessage = $this->createSpecMessage(
            $spec,
            $contents,
            $whitelist,
            $expected,
            $actual,
            $expectedRegisteredClasses,
            $expectedRegisteredFunctions
        );

        $this->assertSame(
            $expected,
            $actual,
            $specMessage
        );
        $this->assertSame(
            $whitelist->getRecordedWhitelistedClasses(),
            $expectedRegisteredClasses,
            $specMessage
        );
        $this->assertSame(
            $whitelist->getRecordedWhitelistedFunctions(),
            $expectedRegisteredFunctions,
            $specMessage
        );
    }

    public function provideValidFiles()
    {
        $files = (new Finder())->files()->in(self::SECONDARY_SPECS_PATH);

        if (0 === count($files)) {
            $files = (new Finder())->files()->in(self::SPECS_PATH);
        }

        $files->sortByName();

        foreach ($files as $file) {
            try {
                $fixtures = include $file;

                $meta = $fixtures['meta'];
                unset($fixtures['meta']);

                foreach ($fixtures as $fixtureTitle => $fixtureSet) {
                    yield $this->parseSpecFile($meta, $fixtureTitle, $fixtureSet)->current();
                }
            } catch (Throwable $throwable) {
                $this->fail(
                    sprintf(
                        'An error occurred while parsing the file "%s": %s',
                        $file,
                        $throwable->getMessage()
                    )
                );
            }
        }
    }

    private function createScoper(string $contents): Scoper
    {
        $astLocator = (new BetterReflection())->astLocator();

        $sourceLocator = new AggregateSourceLocator([
            new StringSourceLocator($contents, $astLocator),
            new PhpInternalSourceLocator($astLocator),
        ]);

        $classReflector = new ClassReflector($sourceLocator);

        return new PhpScoper(
            create_parser(),
            new FakeScoper(),
            new TraverserFactory(
                new Reflector(
                    $classReflector,
                    new FunctionReflector($sourceLocator, $classReflector)
                )
            )
        );
    }

    /**
     * @param array        $meta
     * @param string|int   $fixtureTitle
     * @param string|array $fixtureSet
     *
     * @return Generator
     */
    private function parseSpecFile(array $meta, $fixtureTitle, $fixtureSet): Generator
    {
        $spec = sprintf(
            '[%s] %s',
            $meta['title'],
            isset($fixtureSet['spec']) ? $fixtureSet['spec'] : $fixtureTitle
        );

        $payload = is_string($fixtureSet) ? $fixtureSet : $fixtureSet['payload'];

        $payloadParts = preg_split("/\n----(?:\n|$)/", $payload);

        $this->assertSame(
            [],
            $diff = array_diff(
                array_keys($meta),
                self::SPECS_META_KEYS
            ),
            sprintf(
                'Expected the keys found in the meta section to be known keys, unknown keys: "%s"',
                implode('", "', $diff)
            )
        );

        if (is_array($fixtureSet)) {
            $this->assertSame(
                [],
                $diff = array_diff(
                    array_keys($fixtureSet),
                    self::SPECS_SPEC_KEYS
                ),
                sprintf(
                    'Expected the keys found in the spec section to be known keys, unknown keys: "%s"',
                    implode('", "', $diff)
                )
            );
        }

        yield [
            $spec,
            $payloadParts[0],   // Input
            $fixtureSet['prefix'] ?? $meta['prefix'],
            Whitelist::create(
                $fixtureSet['whitelist-global-constants'] ?? $meta['whitelist-global-constants'],
                $fixtureSet['whitelist-global-functions'] ?? $meta['whitelist-global-functions'],
                ...($fixtureSet['whitelist'] ?? $meta['whitelist'])
            ),
            '' === $payloadParts[1] ? null : $payloadParts[1],   // Expected output; null means an exception is expected,
            $fixtureSet['registered-classes'] ?? $meta['registered-classes'],
            $fixtureSet['registered-functions'] ?? $meta['registered-functions'],
        ];
    }

    /**
     * @param string[][] $expectedRegisteredClasses
     * @param string[][] $expectedRegisteredFunctions
     */
    private function createSpecMessage(
        string $spec,
        string $contents,
        Whitelist $whitelist,
        ?string $expected,
        ?string $actual,
        array $expectedRegisteredClasses,
        array $expectedRegisteredFunctions
    ): string
    {
        $formattedWhitelist = $this->formatSimpleList($whitelist->toArray());

        $formattedWhitelistGlobalConstants = $this->convertBoolToString($whitelist->whitelistGlobalConstants());
        $formattedWhitelistGlobalFunctions = $this->convertBoolToString($whitelist->whitelistGlobalFunctions());

        $whitelist->getRecordedWhitelistedFunctions();
        $whitelist->getRecordedWhitelistedClasses();

        $formattedExpectedRegisteredClasses = $this->formatTupleList($expectedRegisteredClasses);
        $formattedExpectedRegisteredFunctions = $this->formatTupleList($expectedRegisteredFunctions);

        $formattedActualRegisteredClasses = $this->formatTupleList($whitelist->getRecordedWhitelistedClasses());
        $formattedActualRegisteredFunctions = $this->formatTupleList($whitelist->getRecordedWhitelistedFunctions());

        $titleSeparator = str_repeat(
            '=',
            min(
                strlen($spec),
                80
            )
        );

        return <<<OUTPUT
$titleSeparator
SPECIFICATION
$titleSeparator
$spec

$titleSeparator
INPUT
whitelist: $formattedWhitelist
whitelist global constants: $formattedWhitelistGlobalConstants
whitelist global functions: $formattedWhitelistGlobalFunctions
$titleSeparator
$contents

$titleSeparator
EXPECTED
$titleSeparator
$expected
----------------
registered classes: $formattedExpectedRegisteredClasses
registered functions: $formattedExpectedRegisteredFunctions

$titleSeparator
ACTUAL
$titleSeparator
$actual
----------------
registered classes: $formattedActualRegisteredClasses
registered functions: $formattedActualRegisteredFunctions

-------------------------------------------------------------------------------
OUTPUT
        ;
    }

    /**
     * @param string[] $strings
     */
    private function formatSimpleList(array $strings): string
    {
        if (0 === count($strings)) {
            return '[]';
        }

        if (1 === count($strings)) {
            return '['.current($strings).']';
        }

        return sprintf(
            "[\n%s\n]",
            implode(
                PHP_EOL,
                array_map(
                    function (string $string): string {
                        return '  - '.$string;
                    },
                    $strings
                )
            )
        );
    }

    /**
     * @param string[][] $stringTuples
     */
    private function formatTupleList(array $stringTuples): string
    {
        if (0 === count($stringTuples)) {
            return '[]';
        }

        if (1 === count($stringTuples)) {
            /** @var string[] $tuple */
            $tuple = current($stringTuples);

            return sprintf('[%s => %s]', ...$tuple);
        }

        return sprintf(
            "[\n%s\n]",
            implode(
                PHP_EOL,
                array_map(
                    function (array $stringTuple): string {
                        return sprintf('  - %s => %s', ...$stringTuple);
                    },
                    $stringTuples
                )
            )
        );
    }

    private function convertBoolToString(bool $bool): string
    {
        return true === $bool ? 'true' : 'false';
    }
}
