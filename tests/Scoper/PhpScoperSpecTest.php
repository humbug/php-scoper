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
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Scoper\Spec\Meta;
use Humbug\PhpScoper\Scoper\Spec\SpecFinder;
use Humbug\PhpScoper\Scoper\Spec\SpecWithConfig;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Throwable;
use UnexpectedValueException;
use function array_filter;
use function array_map;
use function array_merge;
use function array_slice;
use function array_values;
use function basename;
use function count;
use function current;
use function explode;
use function implode;
use function is_string;
use function min;
use function rtrim;
use function sprintf;
use function str_repeat;
use function str_starts_with;
use function strlen;
use function usort;
use const PHP_EOL;
use const PHP_VERSION_ID;

/**
 * @internal
 */
#[Group('integration')]
class PhpScoperSpecTest extends TestCase
{
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
        $files = SpecFinder::findTmpSpecFiles();

        self::assertCount(0, $files);
    }

    #[DataProvider('provideValidFiles')]
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
            $actual = self::trimTrailingSpaces(
                $scoper->scope($filePath, $contents),
            );

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
            if (!str_starts_with($error->getMessage(), 'Syntax error,')) {
                throw new Error(
                    sprintf(
                        'Could not parse the spec %s: %s',
                        $spec,
                        $error->getMessage(),
                    ),
                    0,
                    $error,
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
                        array_slice($lines, $startLine, $endLine - $startLine + 1),
                    ),
                ),
            );
        } catch (Throwable $throwable) {
            throw new Error(
                sprintf(
                    'Could not parse the spec %s: %s',
                    $spec,
                    $throwable->getMessage().$throwable->getTraceAsString(),
                ),
                0,
                $throwable,
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
            $expectedRegisteredFunctions,
        );

        self::assertSame($expected, $actual, $specMessage);

        $actualRecordedExposedClasses = $symbolsRegistry->getRecordedClasses();

        self::assertSameRecordedSymbols($expectedRegisteredClasses, $actualRecordedExposedClasses, $specMessage);

        $actualRecordedExposedFunctions = $symbolsRegistry->getRecordedFunctions();

        self::assertSameRecordedSymbols($expectedRegisteredFunctions, $actualRecordedExposedFunctions, $specMessage);
    }

    public static function provideValidFiles(): iterable
    {
        [$sourceDir, $files] = SpecFinder::findSpecFiles();

        foreach ($files as $file) {
            try {
                // TODO: can extract rename
                $specs = include $file;

                $meta = $specs['meta'];
                unset($specs['meta']);

                foreach ($specs as $title => $spec) {
                    yield from self::parseSpec(
                        basename($sourceDir).'/'.$file->getRelativePathname(),
                        $meta,
                        $title,
                        $spec,
                    );
                }
            } catch (Throwable $throwable) {
                self::fail(
                    sprintf(
                        'An error occurred while parsing the file "%s": %s%s%s',
                        $file,
                        $throwable->getMessage(),
                        "\n\n",
                        $throwable->getTraceAsString(),
                    ),
                );
            }
        }
    }

    private static function createScoper(
        string $prefix,
        SymbolsConfiguration $symbolsConfiguration,
        SymbolsRegistry $symbolsRegistry
    ): Scoper {
        $container = new Container();

        $reflector = Reflector::createWithPhpStormStubs()
            ->withAdditionalSymbols(
                $symbolsConfiguration->getExcludedClasses(),
                $symbolsConfiguration->getExcludedFunctions(),
                $symbolsConfiguration->getExcludedConstants(),
            );

        $enrichedReflector = new EnrichedReflector(
            $reflector,
            $symbolsConfiguration,
        );

        return new PhpScoper(
            $container->getParser(),
            new FakeScoper(),
            new TraverserFactory(
                $enrichedReflector,
                $prefix,
                $symbolsRegistry,
            ),
            $container->getPrinter(),
            $container->getLexer(),
        );
    }

    // TODO: can extract CS
    private static function parseSpec(
        string $file,
        Meta $meta,
        int|string $title,
        SpecWithConfig|string $specWithConfigOrSimpleSpec,
    ): iterable {
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

        $specWithConfig = is_string($specWithConfigOrSimpleSpec)
            ? SpecWithConfig::fromSimpleSpec($specWithConfigOrSimpleSpec)
            : $specWithConfigOrSimpleSpec;

        // TODO: can extract this change, 'spec' is unused
        $completeTitle = sprintf(
            '[%s] %s',
            $meta->title,
            $title,
        );

        yield [
            $file,
            $completeTitle,
            $specWithConfig->inputCode,
            $specWithConfigOrSimpleSpec->prefix ?? $meta->prefix,
            self::createSymbolsConfiguration($specWithConfig, $meta),
            $specWithConfig->expectedOutputCode,
            $specWithConfigOrSimpleSpec->expectedRecordedClasses ?? $meta->expectedRecordedClasses,
            $specWithConfigOrSimpleSpec->expectedRecordedFunctions ?? $meta->expectedRecordedFunctions,
            $specWithConfigOrSimpleSpec->minPhpVersion ?? $meta->minPhpVersion,
            $specWithConfigOrSimpleSpec->maxPhpVersion ?? $meta->maxPhpVersion,
        ];
    }

    private static function createSymbolsConfiguration(
        SpecWithConfig $specWithConfig,
        Meta $meta
    ): SymbolsConfiguration {
        $mergedConfig = array_merge(
            $meta->getSymbolsConfig(),
            $specWithConfig->getSymbolsConfig(),
        );

        return (new SymbolsConfigurationFactory(new RegexChecker()))->createSymbolsConfiguration($mergedConfig);
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

        $formattedInternalClasses = self::formatSymbolRegistry($symbolsConfiguration->getExcludedClasses());
        $formattedInternalFunctions = self::formatSymbolRegistry($symbolsConfiguration->getExcludedFunctions());
        $formattedInternalConstants = self::formatSymbolRegistry($symbolsConfiguration->getExcludedConstants());

        $formattedExpectedRegisteredClasses = self::formatTupleList($expectedRegisteredClasses);
        $formattedExpectedRegisteredFunctions = self::formatTupleList($expectedRegisteredFunctions);

        $formattedActualRegisteredClasses = self::formatTupleList($symbolsRegistry->getRecordedClasses());
        $formattedActualRegisteredFunctions = self::formatTupleList($symbolsRegistry->getRecordedFunctions());

        $titleSeparator = str_repeat(
            '=',
            min(
                strlen($spec),
                80,
            ),
        );

        return <<<OUTPUT
            {$titleSeparator}
            SPECIFICATION
            {$titleSeparator}
            {$spec}
            {$file}

            {$titleSeparator}
            INPUT
            expose global classes: {$formattedExposeGlobalClasses}
            expose global functions: {$formattedExposeGlobalFunctions}
            expose global constants: {$formattedExposeGlobalConstants}

            exclude namespaces: {$formattedNamespacesToExclude}
            expose namespaces: {$formattedNamespacesToExpose}

            expose classes: {$formattedClassesToExpose}
            expose functions: {$formattedFunctionsToExpose}
            expose constants: {$formattedConstantsToExpose}

            (raw) internal classes: {$formattedInternalClasses}
            (raw) internal functions: {$formattedInternalFunctions}
            (raw) internal constants: {$formattedInternalConstants}
            {$titleSeparator}
            {$contents}

            {$titleSeparator}
            EXPECTED
            {$titleSeparator}
            {$expected}
            ----------------
            recorded functions: {$formattedExpectedRegisteredFunctions}
            recorded classes: {$formattedExpectedRegisteredClasses}

            {$titleSeparator}
            ACTUAL
            {$titleSeparator}
            {$actual}
            ----------------
            recorded functions: {$formattedActualRegisteredFunctions}
            recorded classes: {$formattedActualRegisteredClasses}

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
                    static fn (string $string): string => '  - '.$string,
                    $strings,
                ),
            ),
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
                    static fn (array $stringTuple): string => sprintf('  - %s => %s', ...$stringTuple),
                    $stringTuples,
                ),
            ),
        );
    }

    private static function trimTrailingSpaces(string $value): string
    {
        return implode(
            "\n",
            array_map(
                rtrim(...),
                explode("\n", $value),
            ),
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
