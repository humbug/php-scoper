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

namespace Humbug\PhpScoper\Scoper\Spec;

use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\SplFileInfo;
use function basename;
use function iter\toArrayWithKeys;

/**
 * @internal
 */
#[CoversClass(SpecParser::class)]
final class SpecParserTest extends TestCase
{
    private const FIXTURE_DIR = __DIR__.'/Fixtures';

    #[DataProvider('specProvider')]
    public function test_it_can_parse_spec_files(
        string $path,
        array $expected,
    ): void {
        $actual = toArrayWithKeys(
            SpecParser::parseSpecFile(
                self::FIXTURE_DIR,
                self::createSpecSplFileInfo($path),
            ),
        );

        self::assertEquals($expected, $actual);
    }

    public static function specProvider(): iterable
    {
        $specCode = <<<'PHP'
            echo "Hello world!";

            PHP;

        $expectedCode = <<<'PHP'
            namespace Humbug;

            echo "Hello world!";

            PHP;

        yield [
            self::FIXTURE_DIR.'/simple-spec-file.php',
            [
                [
                    'Fixtures/simple-spec-file.php',
                    '[Example of simple spec file] 0',
                    $specCode,
                    'Humbug',
                    SymbolsConfiguration::create(
                        exposeGlobalConstants: false,
                        exposeGlobalFunctions: false,
                        exposeGlobalClasses: false,
                    ),
                    $expectedCode,
                    [],
                    [],
                    null,
                    null,
                ],
                [
                    'Fixtures/simple-spec-file.php',
                    '[Example of simple spec file] A spec with a title',
                    $specCode,
                    'Humbug',
                    SymbolsConfiguration::create(
                        exposeGlobalConstants: false,
                        exposeGlobalFunctions: false,
                        exposeGlobalClasses: false,
                    ),
                    $expectedCode,
                    [],
                    [],
                    null,
                    null,
                ],
            ],
        ];

        yield [
            self::FIXTURE_DIR.'/complete-spec-file.php',
            [
                [
                    'Fixtures/complete-spec-file.php',
                    '[Example of simple spec file] Spec with default meta values',
                    $specCode,
                    'Humbug',
                    self::createSymbolsConfiguration([
                        'expose-global-constants' => true,
                        'expose-global-classes' => true,
                        'expose-global-functions' => true,
                        'expose-namespaces' => ['ExposedNamespace'],
                        'expose-constants' => ['EXPOSED_CONST'],
                        'expose-classes' => ['ExposedClass'],
                        'expose-functions' => ['exposed_function'],
                        'exclude-namespaces' => ['ExcludedNamespace'],
                        'exclude-constants' => ['EXCLUDED_CONST'],
                        'exclude-classes' => ['ExcludedClass'],
                        'exclude-functions' => ['excluded_function'],
                    ]),
                    $expectedCode,
                    ['Acme\RecordedClass', 'Humbug\Acme\RecordedClass'],
                    ['Acme\recorded_function', 'Humbug\Acme\recorded_function'],
                    72_000,
                    83_000,
                ],
                [
                    'Fixtures/complete-spec-file.php',
                    '[Example of simple spec file] Spec with the more verbose form',
                    $specCode,
                    'Humbug',
                    self::createSymbolsConfiguration([
                        'expose-global-constants' => true,
                        'expose-global-classes' => true,
                        'expose-global-functions' => true,
                        'expose-namespaces' => ['ExposedNamespace'],
                        'expose-constants' => ['EXPOSED_CONST'],
                        'expose-classes' => ['ExposedClass'],
                        'expose-functions' => ['exposed_function'],
                        'exclude-namespaces' => ['ExcludedNamespace'],
                        'exclude-constants' => ['EXCLUDED_CONST'],
                        'exclude-classes' => ['ExcludedClass'],
                        'exclude-functions' => ['excluded_function'],
                    ]),
                    $expectedCode,
                    ['Acme\RecordedClass', 'Humbug\Acme\RecordedClass'],
                    ['Acme\recorded_function', 'Humbug\Acme\recorded_function'],
                    72_000,
                    83_000,
                ],
                [
                    'Fixtures/complete-spec-file.php',
                    '[Example of simple spec file] Spec with overridden meta values',
                    $specCode,
                    'AnotherPrefix',
                    self::createSymbolsConfiguration([
                        'expose-global-constants' => false,
                        'expose-global-classes' => false,
                        'expose-global-functions' => false,
                        'expose-namespaces' => ['AnotherExposedNamespace'],
                        'expose-constants' => ['ANOTHER_EXPOSED_CONST'],
                        'expose-classes' => ['AnotherExposedClass'],
                        'expose-functions' => ['another_exposed_function'],
                        'exclude-namespaces' => ['AnotherExcludedNamespace'],
                        'exclude-constants' => ['ANOTHER_EXCLUDED_CONST'],
                        'exclude-classes' => ['AnotherExcludedClass'],
                        'exclude-functions' => ['another_excluded_function'],
                    ]),
                    $expectedCode,
                    ['AnotherRecordedClass'],
                    ['AnotherRecordedFunction'],
                    73_000,
                    82_000,
                ],
            ],
        ];
    }

    private static function createSpecSplFileInfo(string $path): SplFileInfo
    {
        return new SplFileInfo(
            $path,
            Path::makeRelative($path, self::FIXTURE_DIR),
            basename($path),
        );
    }

    private static function createSymbolsConfiguration(array $config): SymbolsConfiguration
    {
        static $factory;

        if (!isset($factory)) {
            $factory = new SymbolsConfigurationFactory(new RegexChecker());
        }

        return $factory->createSymbolsConfiguration($config);
    }
}
