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
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Scoper\Spec\SpecFinder;
use Humbug\PhpScoper\Scoper\Spec\SpecFormatter;
use Humbug\PhpScoper\Scoper\Spec\SpecParser;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Throwable;
use UnexpectedValueException;
use function array_filter;
use function array_map;
use function array_slice;
use function array_values;
use function explode;
use function implode;
use function rtrim;
use function sprintf;
use function str_starts_with;
use function usort;
use const PHP_VERSION_ID;

/**
 * @internal
 */
#[Group('integration')]
class PhpScoperSpecTest extends TestCase
{
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

        $specMessage = SpecFormatter::createSpecMessage(
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
            yield SpecParser::parseSpecFile(
                $sourceDir,
                $file,
            );
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
