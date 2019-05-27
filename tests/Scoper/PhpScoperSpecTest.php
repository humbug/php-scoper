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

use Error;
use Generator;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\ReflectorFactory;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;
use UnexpectedValueException;
use const PHP_EOL;
use function array_diff;
use function array_filter;
use function array_keys;
use function array_map;
use function array_slice;
use function array_values;
use function basename;
use function current;
use function explode;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_parser;
use function implode;
use function is_array;
use function sprintf;
use function strpos;
use function usort;

class PhpScoperSpecTest extends TestCase
{
    private const SPECS_PATH = __DIR__.'/../../specs';
    private const SECONDARY_SPECS_PATH = __DIR__.'/../../_specs';

    private const SPECS_META_KEYS = [
        'title',
        'prefix',
        'whitelist',
        'whitelist-global-constants',
        'whitelist-global-classes',
        'whitelist-global-functions',
        'registered-classes',
        'registered-functions',
    ];

    private const SPECS_SPEC_KEYS = [
        'prefix',
        'whitelist',
        'whitelist-global-constants',
        'whitelist-global-classes',
        'whitelist-global-functions',
        'registered-classes',
        'registered-functions',
        'payload',
    ];

    /**
     * This test is to ensure no file is left in _specs for the CI. It is fine otherwise for this test to fail locally
     * when developing something.
     */
    public function test_it_uses_the_right_specs_directory(): void
    {
        $files = (new Finder())->files()->in(self::SECONDARY_SPECS_PATH);

        $this->assertCount(0, $files);
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(
        string $file,
        string $spec,
        string $contents,
        string $prefix,
        Whitelist $whitelist,
        ?string $expected,
        array $expectedRegisteredClasses,
        array $expectedRegisteredFunctions
    ): void {
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
        } catch (PhpParserError $error) {
            if (0 !== strpos($error->getMessage(), 'Syntax error,')) {
                throw new Error(
                    sprintf(
                        'Could not parse the spec %s: %s',
                        $spec,
                        $error->getMessage()
                    ),
                    0,
                    $error
                );
            }

            $lines = array_values(array_filter(explode("\n", $contents)));

            $startLine = $error->getAttributes()['startLine'] - 1;
            $endLine = $error->getAttributes()['endLine'] + 1;

            $this->fail(
                sprintf(
                    'Unexpected parse error found in the following lines: %s%s%s',
                    $error->getMessage(),
                    "\n\n> ",
                    implode(
                        "\n> ",
                        array_slice($lines, $startLine, $endLine - $startLine + 1)
                    )
                )
            );
        } catch (Throwable $throwable) {
            throw new Error(
                sprintf(
                    'Could not parse the spec %s: %s',
                    $spec,
                    $throwable->getMessage()
                ),
                0,
                $throwable
            );
        }

        $specMessage = $this->createSpecMessage(
            $file,
            $spec,
            $contents,
            $whitelist,
            $expected,
            $actual,
            $expectedRegisteredClasses,
            $expectedRegisteredFunctions
        );

        $this->assertSame($expected, $actual, $specMessage);

        $actualRecordedWhitelistedClasses = $whitelist->getRecordedWhitelistedClasses();

        $this->assertSameRecordedSymbols($actualRecordedWhitelistedClasses, $expectedRegisteredClasses, $specMessage);

        $actualRecordedWhitelistedFunctions = $whitelist->getRecordedWhitelistedFunctions();

        $this->assertSameRecordedSymbols($actualRecordedWhitelistedFunctions, $expectedRegisteredFunctions, $specMessage);
    }

    public function provideValidFiles(): Generator
    {
        $sourceDir = self::SECONDARY_SPECS_PATH;

        $files = (new Finder())->files()->in($sourceDir);

        if (0 === count($files)) {
            $sourceDir = self::SPECS_PATH;

            $files = (new Finder())->files()->in($sourceDir);
        }

        $files->sortByName();

        foreach ($files as $file) {
            /* @var SplFileInfo $file */
            try {
                $fixtures = include $file;

                $meta = $fixtures['meta'];
                unset($fixtures['meta']);

                foreach ($fixtures as $fixtureTitle => $fixtureSet) {
                    yield $this->parseSpecFile(
                        basename($sourceDir).'/'.$file->getRelativePathname(),
                        $meta,
                        $fixtureTitle,
                        $fixtureSet
                    )->current();
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
        $phpParser = create_parser();

        return new PhpScoper(
            $phpParser,
            new FakeScoper(),
            new TraverserFactory(ReflectorFactory::create(
                $contents,
                $phpParser
            ))
        );
    }

    /**
     * @param string|int   $fixtureTitle
     * @param string|array $fixtureSet
     */
    private function parseSpecFile(string $file, array $meta, $fixtureTitle, $fixtureSet): Generator
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
            $file,
            $spec,
            $payloadParts[0],   // Input
            $fixtureSet['prefix'] ?? $meta['prefix'],
            Whitelist::create(
                $fixtureSet['whitelist-global-constants'] ?? $meta['whitelist-global-constants'],
                $fixtureSet['whitelist-global-classes'] ?? $meta['whitelist-global-classes'],
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
        string $file,
        string $spec,
        string $contents,
        Whitelist $whitelist,
        ?string $expected,
        ?string $actual,
        array $expectedRegisteredClasses,
        array $expectedRegisteredFunctions
    ): string {
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
$file

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
                    static function (string $string): string {
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
                    static function (array $stringTuple): string {
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

    /**
     * @param string[][] $expected
     * @param string[][] $actual
     */
    private function assertSameRecordedSymbols(array $expected, array $actual, string $message): void
    {
        $sort = static function (array $a, array $b): int {
            /*
             * @var string[] $a
             * @var string[] $b
             */

            return $a[0] <=> $b[0];
        };

        usort($expected, $sort);
        usort($actual, $sort);

        $this->assertSame($expected, $actual, $message);
    }
}
