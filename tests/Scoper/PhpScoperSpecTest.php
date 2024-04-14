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

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\SpecFramework\SpecFinder;
use Humbug\PhpScoper\SpecFramework\SpecNormalizer;
use Humbug\PhpScoper\SpecFramework\SpecParser;
use Humbug\PhpScoper\SpecFramework\SpecScenario;
use Humbug\PhpScoper\SpecFramework\Throwable\UnparsableSpec;
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
use function array_slice;
use function array_values;
use function explode;
use function implode;
use function sprintf;
use function str_starts_with;

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
    public function test_can_scope_valid_files(SpecScenario $scenario): void
    {
        $scenario->checkPHPVersionRequirements();

        $filePath = 'file.php';
        $symbolsRegistry = new SymbolsRegistry();

        $scoper = self::createScoper(
            $scenario->prefix,
            $scenario->symbolsConfiguration,
            $symbolsRegistry,
        );

        try {
            $actual = SpecNormalizer::trimTrailingSpaces(
                $scoper->scope($filePath, $scenario->inputCode),
            );

            $scenario->failIfExpectedFailure($this);
        } catch (UnexpectedValueException $exception) {
            $scenario->assertExpectedFailure($this, $exception);

            return;
        } catch (PhpParserError $error) {
            self::handlePhpParserError($scenario, $error);
        } catch (Throwable $throwable) {
            throw UnparsableSpec::create($scenario->title, $throwable);
        }

        $scenario->assertExpectedResult(
            $this,
            $symbolsRegistry,
            $actual,
        );
    }

    public static function provideValidFiles(): iterable
    {
        [$sourceDir, $files] = SpecFinder::findSpecFiles();

        foreach ($files as $file) {
            $scenarios = SpecParser::parseSpecFile($sourceDir, $file);

            foreach ($scenarios as $label => $scenario) {
                yield $label => [$scenario];
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

    private static function handlePhpParserError(
        SpecScenario $scenario,
        PhpParserError $error,
    ): never {
        if (!str_starts_with($error->getMessage(), 'Syntax error,')) {
            throw UnparsableSpec::create($scenario->title, $error);
        }

        $lines = array_values(array_filter(explode("\n", $scenario->inputCode)));

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
    }
}
