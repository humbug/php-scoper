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
use Humbug\PhpScoper\Configuration\ConfigurationKeys;
use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use Humbug\PhpScoper\Whitelist;
use InvalidArgumentException;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;
use UnexpectedValueException;
use function array_diff;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_slice;
use function array_values;
use function basename;
use function count;
use function current;
use function explode;
use function Humbug\PhpScoper\create_parser;
use function implode;
use function is_array;
use function is_string;
use function min;
use function Safe\preg_split;
use function Safe\sprintf;
use function Safe\usort;
use function str_repeat;
use function strlen;
use function strpos;
use const PHP_EOL;
use const PHP_VERSION_ID;

/**
 * @group integration
 */
class PhpScoperSpecTest extends TestCase
{
    private const SPECS_PATH = __DIR__.'/../../specs';
    private const SECONDARY_SPECS_PATH = __DIR__.'/../../_specs';

    private const SPECS_META_KEYS = [
        'minPhpVersion',
        'maxPhpVersion',
        'title',
        ConfigurationKeys::PREFIX_KEYWORD,
        // SPECS_CONFIG_KEYS included
        'expected-recorded-classes',
        'expected-recorded-functions',
    ];

    // Keys allowed on a spec level
    private const SPECS_SPEC_KEYS = [
        ConfigurationKeys::PREFIX_KEYWORD,
        // SPECS_CONFIG_KEYS included
        'expected-recorded-classes',
        'expected-recorded-functions',
        'payload',
    ];

    // Keys kept and used to build the symbols configuration
    private const SPECS_CONFIG_KEYS = [
        ConfigurationKeys::WHITELIST_KEYWORD,

        ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD,
        ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD,
        ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD,

        ConfigurationKeys::EXPOSE_NAMESPACES_KEYWORD,
        ConfigurationKeys::EXPOSE_CLASSES_SYMBOLS_KEYWORD,
        ConfigurationKeys::EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD,
        ConfigurationKeys::EXPOSE_CONSTANTS_SYMBOLS_KEYWORD,

        ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD,
        ConfigurationKeys::CLASSES_INTERNAL_SYMBOLS_KEYWORD,
        ConfigurationKeys::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD,
        ConfigurationKeys::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD,
    ];

    /**
     * This test is to ensure no file is left in _specs for the CI. It is fine otherwise for this test to fail locally
     * when developing something.
     */
    public function test_it_uses_the_right_specs_directory(): void
    {
        $files = (new Finder())->files()->in(self::SECONDARY_SPECS_PATH);

        self::assertCount(0, $files);
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(
        string $file,
        string $spec,
        string $contents,
        string $prefix,
        SymbolsConfiguration $symbolsConfiguration,
        ?string $expected,
        array $expectedRegisteredClasses,
        array $expectedRegisteredFunctions,
        ?int $minPhpVersion,
        ?int $maxPhpVersion
    ): void {
        if (null !== $minPhpVersion && $minPhpVersion > PHP_VERSION_ID) {
            self::markTestSkipped(sprintf('Min PHP version not matched for spec %s', $spec));
        }

        if (null !== $maxPhpVersion && $maxPhpVersion <= PHP_VERSION_ID) {
            self::markTestSkipped(sprintf('Max PHP version not matched for spec %s', $spec));
        }

        $filePath = 'file.php';

        $symbolsRegistry = new SymbolsRegistry();

        $scoper = self::createScoper(
            $prefix,
            $symbolsConfiguration,
            $symbolsRegistry,
        );

        try {
            $actual = $scoper->scope($filePath, $contents);

            if (null === $expected) {
                self::fail('Expected exception to be thrown.');
            }
        } catch (UnexpectedValueException $exception) {
            if (null !== $expected) {
                throw $exception;
            }

            self::assertTrue(true);

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

            self::fail(
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
                    $throwable->getMessage().$throwable->getTraceAsString()
                ),
                0,
                $throwable
            );
        }

        $specMessage = self::createSpecMessage(
            $file,
            $spec,
            $contents,
            $symbolsConfiguration,
            $symbolsRegistry,
            $expected,
            $actual,
            $expectedRegisteredClasses,
            $expectedRegisteredFunctions
        );

        self::assertSame($expected, $actual, $specMessage);

        $actualRecordedExposedClasses = $symbolsRegistry->getRecordedClasses();

        self::assertSameRecordedSymbols($expectedRegisteredClasses, $actualRecordedExposedClasses, $specMessage);

        $actualRecordedExposedFunctions = $symbolsRegistry->getRecordedFunctions();

        self::assertSameRecordedSymbols($expectedRegisteredFunctions, $actualRecordedExposedFunctions, $specMessage);
    }

    public static function provideValidFiles(): iterable
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
                    yield from self::parseSpecFile(
                        basename($sourceDir).'/'.$file->getRelativePathname(),
                        $meta,
                        $fixtureTitle,
                        $fixtureSet,
                    );
                }
            } catch (Throwable $throwable) {
                self::fail(
                    sprintf(
                        'An error occurred while parsing the file "%s": %s',
                        $file,
                        $throwable->getMessage(),
                    ),
                );
            }
        }
    }

    private static function createScoper(
        string $prefix,
        SymbolsConfiguration $symbolsConfiguration,
        SymbolsRegistry $symbolsRegistry
    ): Scoper
    {
        $phpParser = create_parser();

        $reflector = Reflector
            ::createWithPhpStormStubs()
            ->withSymbols(
                $symbolsConfiguration->getExcludedClassNames(),
                $symbolsConfiguration->getExcludedFunctionNames(),
                $symbolsConfiguration->getExcludedConstantNames(),
            );

        $enrichedReflector = new EnrichedReflector(
            $reflector,
            $symbolsConfiguration,
        );

        return new PhpScoper(
            $phpParser,
            new FakeScoper(),
            new TraverserFactory(
                $enrichedReflector,
                $prefix,
                $symbolsRegistry,
            ),
            $prefix,
            $symbolsRegistry,
        );
    }

    /**
     * @param string|int   $fixtureTitle
     * @param string|array $fixtureSet
     */
    private static function parseSpecFile(string $file, array $meta, $fixtureTitle, $fixtureSet): iterable
    {
        static $specMetaKeys;
        static $specKeys;

        if (!isset($specMetaKeys)) {
            $specMetaKeys = [
                ...self::SPECS_META_KEYS,
                ...self::SPECS_CONFIG_KEYS,
            ];
        }

        if (!isset($specKeys)) {
            $specKeys = [
                ...self::SPECS_SPEC_KEYS,
                ...self::SPECS_CONFIG_KEYS,
            ];
        }

        $spec = sprintf(
            '[%s] %s',
            $meta['title'],
            isset($fixtureSet['spec']) ? $fixtureSet['spec'] : $fixtureTitle
        );

        $payload = is_string($fixtureSet) ? $fixtureSet : $fixtureSet['payload'];

        $payloadParts = preg_split("/\n----(?:\n|$)/", $payload);

        self::assertSame(
            [],
            $diff = array_diff(
                array_keys($meta),
                $specMetaKeys,
            ),
            sprintf(
                'Expected the keys found in the meta section to be known keys, unknown keys: "%s"',
                implode('", "', $diff)
            )
        );

        if (is_array($fixtureSet)) {
            $diff = array_diff(
                array_keys($fixtureSet),
                $specKeys,
            );

            if ([ConfigurationKeys::WHITELIST_KEYWORD] === array_values($diff)) {
                $diff = [];
            }

            self::assertSame(
                [],
                $diff,
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
            $fixtureSet[ConfigurationKeys::PREFIX_KEYWORD] ?? $meta[ConfigurationKeys::PREFIX_KEYWORD],
            self::createSymbolsConfiguration(
                $file,
                is_string($fixtureSet) ? [] : $fixtureSet,
                $meta,
            ),
            '' === $payloadParts[1] ? null : $payloadParts[1],   // Expected output; null means an exception is expected,
            $fixtureSet['expected-recorded-classes'] ?? $meta['expected-recorded-classes'],
            $fixtureSet['expected-recorded-functions'] ?? $meta['expected-recorded-functions'],
            $meta['minPhpVersion'] ?? null,
            $meta['maxPhpVersion'] ?? null,
        ];
    }

    /**
     * @param string|array $fixtureSet
     */
    private static function createSymbolsConfiguration(
        string $file,
        $fixtureSet,
        array $meta
    ): SymbolsConfiguration
    {
        if (is_string($fixtureSet)) {
            $fixtureSet = [];
        }

        $mergedConfig = array_merge($meta, $fixtureSet);

        $config = [];

        foreach (self::SPECS_CONFIG_KEYS as $key) {
            if (!array_key_exists($key, $mergedConfig)) {
                if ($key === ConfigurationKeys::WHITELIST_KEYWORD) {
                    continue;
                }

                throw new InvalidArgumentException(
                    sprintf(
                        'Missing the key "%s" for the file "%s"',
                        $key,
                        $file,
                    ),
                );
            }

            $config[$key] = $mergedConfig[$key];
        }

        return (new SymbolsConfigurationFactory(new RegexChecker()))->createSymbolsConfiguration($config);
    }

    /**
     * @param string[][] $expectedRegisteredClasses
     * @param string[][] $expectedRegisteredFunctions
     */
    private static function createSpecMessage(
        string $file,
        string $spec,
        string $contents,
        SymbolsConfiguration $symbolsConfiguration,
        SymbolsRegistry $symbolsRegistry,
        ?string $expected,
        ?string $actual,
        array $expectedRegisteredClasses,
        array $expectedRegisteredFunctions
    ): string {
        $formattedExposeGlobalClasses = self::convertBoolToString($symbolsConfiguration->shouldExposeGlobalClasses());
        $formattedExposeGlobalConstants = self::convertBoolToString($symbolsConfiguration->shouldExposeGlobalConstants());
        $formattedExposeGlobalFunctions = self::convertBoolToString($symbolsConfiguration->shouldExposeGlobalFunctions());

        $formattedNamespacesToExclude = self::formatNamespaceRegistry($symbolsConfiguration->getExcludedNamespaces());
        $formattedNamespacesToExpose = self::formatNamespaceRegistry($symbolsConfiguration->getExposedNamespaces());

        $formattedClassesToExpose = self::formatSymbolRegistry($symbolsConfiguration->getExposedClasses());
        $formattedFunctionsToExpose = self::formatSymbolRegistry($symbolsConfiguration->getExposedFunctions());
        $formattedConstantsToExpose = self::formatSymbolRegistry($symbolsConfiguration->getExposedConstants());

        $formattedInternalClasses = self::formatSimpleList($symbolsConfiguration->getExcludedClassNames());
        $formattedInternalFunctions = self::formatSimpleList($symbolsConfiguration->getExcludedFunctionNames());
        $formattedInternalConstants = self::formatSimpleList($symbolsConfiguration->getExcludedConstantNames());

        $formattedExpectedRegisteredClasses = self::formatTupleList($expectedRegisteredClasses);
        $formattedExpectedRegisteredFunctions = self::formatTupleList($expectedRegisteredFunctions);

        $formattedActualRegisteredClasses = self::formatTupleList($symbolsRegistry->getRecordedClasses());
        $formattedActualRegisteredFunctions = self::formatTupleList($symbolsRegistry->getRecordedFunctions());

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
        expose global classes: $formattedExposeGlobalClasses
        expose global functions: $formattedExposeGlobalFunctions
        expose global constants: $formattedExposeGlobalConstants
        
        exclude namespaces: $formattedNamespacesToExclude
        expose namespaces: $formattedNamespacesToExpose
        
        expose classes: $formattedClassesToExpose
        expose functions: $formattedFunctionsToExpose
        expose constants: $formattedConstantsToExpose
        
        (raw) internal classes: $formattedInternalClasses
        (raw) internal functions: $formattedInternalFunctions
        (raw) internal constants: $formattedInternalConstants
        $titleSeparator
        $contents
        
        $titleSeparator
        EXPECTED
        $titleSeparator
        $expected
        ----------------
        recorded functions: $formattedExpectedRegisteredFunctions
        recorded classes: $formattedExpectedRegisteredClasses
        
        $titleSeparator
        ACTUAL
        $titleSeparator
        $actual
        ----------------
        recorded functions: $formattedActualRegisteredFunctions
        recorded classes: $formattedActualRegisteredClasses
        
        -------------------------------------------------------------------------------
        OUTPUT;
    }

    /**
     * @param string[] $strings
     */
    private static function formatSimpleList(array $strings): string
    {
        if (0 === count($strings)) {
            return '[]';
        }

        if (1 === count($strings)) {
            return '[ '.current($strings).' ]';
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
    private static function formatTupleList(array $stringTuples): string
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

    private static function convertBoolToString(bool $bool): string
    {
        return true === $bool ? 'true' : 'false';
    }

    private static function formatNamespaceRegistry(NamespaceRegistry $namespaceRegistry): string
    {
        return self::formatSimpleList([
            ...$namespaceRegistry->getNames(),
            ...$namespaceRegistry->getRegexes(),
        ]);
    }

    private static function formatSymbolRegistry(SymbolRegistry $symbolRegistry): string
    {
        return self::formatSimpleList([
            ...$symbolRegistry->getNames(),
            ...$symbolRegistry->getRegexes(),
        ]);
    }

    /**
     * @param string[][] $expected
     * @param string[][] $actual
     */
    private static function assertSameRecordedSymbols(array $expected, array $actual, string $message): void
    {
        $sort = static fn (array $a, array $b) => $a[0] <=> $b[0];

        usort($expected, $sort);
        usort($actual, $sort);

        self::assertSame($expected, $actual, $message);
    }
}
