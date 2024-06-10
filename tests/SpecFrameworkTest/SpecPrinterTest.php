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

namespace Humbug\PhpScoper\SpecFrameworkTest;

use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\SpecFramework\SpecPrinter;
use Humbug\PhpScoper\SpecFramework\SpecScenario;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SpecPrinter::class)]
final class SpecPrinterTest extends TestCase
{
    #[DataProvider('scenarioProvider')]
    public function test_it_can_print_a_scenario(
        SpecScenario $scenario,
        SymbolsRegistry $symbolsRegistry,
        ?string $actualCode,
        string $expected,
    ): void {
        $actual = SpecPrinter::createSpecMessage(
            $scenario,
            $symbolsRegistry,
            $actualCode,
        );

        self::assertSame($expected, $actual);
    }

    public static function scenarioProvider(): iterable
    {
        $specCode = <<<'PHP'
            echo "Hello world!";

            PHP;

        $expectedCode = <<<'PHP'
            namespace Humbug;

            echo "Hello world!";

            PHP;

        $actualCode = <<<'PHP'
            namespace Prefix;

            echo "Hello world!";

            PHP;

        yield 'simple scenario' => [
            new SpecScenario(
                null,
                null,
                null,
                'Fixtures/complete-spec-file.php',
                '[Example of simple spec file] Spec with the more verbose form',
                $specCode,
                'Humbug',
                self::createSymbolsConfiguration([]),
                $expectedCode,
                [],
                [],
            ),
            new SymbolsRegistry(),
            $actualCode,
            <<<'TEXT'
                =============================================================
                SPECIFICATION
                =============================================================
                [Example of simple spec file] Spec with the more verbose form
                Fixtures/complete-spec-file.php

                =============================================================
                INPUT
                expose global classes: true
                expose global functions: true
                expose global constants: true

                exclude namespaces: []
                expose namespaces: []

                expose classes: []
                expose functions: []
                expose constants: []

                (raw) internal classes: []
                (raw) internal functions: []
                (raw) internal constants: []
                =============================================================
                echo "Hello world!";


                =============================================================
                EXPECTED
                =============================================================
                namespace Humbug;

                echo "Hello world!";

                ----------------
                recorded functions: []
                recorded classes: []

                =============================================================
                ACTUAL
                =============================================================
                namespace Prefix;

                echo "Hello world!";

                ----------------
                recorded functions: []
                recorded classes: []

                -------------------------------------------------------------------------------
                TEXT,
        ];

        yield 'complete scenario without symbols' => [
            new SpecScenario(
                70_200,
                80_300,
                70_400,
                'Fixtures/complete-spec-file.php',
                '[Example of simple spec file] Spec with the more verbose form',
                $specCode,
                'Humbug',
                self::createSymbolsConfiguration([
                    'expose-global-constants' => true,
                    'expose-global-classes' => true,
                    'expose-global-functions' => true,
                    'expose-namespaces' => ['ExposedNamespace'],
                    'expose-constants' => ['EXPOSED_CONSTANT'],
                    'expose-classes' => ['ExposedClass'],
                    'expose-functions' => ['exposed_function'],
                    'exclude-namespaces' => ['ExcludedNamespace'],
                    'exclude-constants' => ['EXCLUDED_CONSTANT'],
                    'exclude-classes' => ['ExcludedClass'],
                    'exclude-functions' => ['excluded_function'],
                ]),
                $expectedCode,
                [['Acme\RecordedClass', 'Humbug\Acme\RecordedClass']],
                [['Acme\recorded_function', 'Humbug\Acme\recorded_function']],
            ),
            new SymbolsRegistry(),
            $actualCode,
            <<<'TEXT'
                =============================================================
                SPECIFICATION
                =============================================================
                [Example of simple spec file] Spec with the more verbose form
                Fixtures/complete-spec-file.php

                =============================================================
                INPUT
                expose global classes: true
                expose global functions: true
                expose global constants: true

                exclude namespaces: [ excludednamespace ]
                expose namespaces: [ exposednamespace ]

                expose classes: [ exposedclass ]
                expose functions: [ exposed_function ]
                expose constants: [ EXPOSED_CONSTANT ]

                (raw) internal classes: [ excludedclass ]
                (raw) internal functions: [ excluded_function ]
                (raw) internal constants: [ EXCLUDED_CONSTANT ]
                =============================================================
                echo "Hello world!";


                =============================================================
                EXPECTED
                =============================================================
                namespace Humbug;

                echo "Hello world!";

                ----------------
                recorded functions: [Acme\recorded_function => Humbug\Acme\recorded_function]
                recorded classes: [Acme\RecordedClass => Humbug\Acme\RecordedClass]

                =============================================================
                ACTUAL
                =============================================================
                namespace Prefix;

                echo "Hello world!";

                ----------------
                recorded functions: []
                recorded classes: []

                -------------------------------------------------------------------------------
                TEXT,
        ];

        yield 'complete scenario with multiple items without symbols' => [
            new SpecScenario(
                70_200,
                80_300,
                70_400,
                'Fixtures/complete-spec-file.php',
                '[Example of simple spec file] Spec with the more verbose form',
                $specCode,
                'Humbug',
                self::createSymbolsConfiguration([
                    'expose-global-constants' => true,
                    'expose-global-classes' => true,
                    'expose-global-functions' => true,
                    'expose-namespaces' => ['ExposedNamespace', 'AnotherExposedNamespace'],
                    'expose-constants' => ['EXPOSED_CONSTANT', 'ANOTHER_EXPOSED_CONSTANT'],
                    'expose-classes' => ['ExposedClass', 'AnotherExposedClass'],
                    'expose-functions' => ['exposed_function', 'another_exposed_function'],
                    'exclude-namespaces' => ['ExcludedNamespace', 'AnotherExcludedNamespace'],
                    'exclude-constants' => ['EXCLUDED_CONSTANT', 'ANOTHER_EXCLUDED_CONSTANT'],
                    'exclude-classes' => ['ExcludedClass', 'AnotherExcludedClass'],
                    'exclude-functions' => ['excluded_function', 'another_excluded_function'],
                ]),
                $expectedCode,
                [
                    ['Acme\RecordedClass', 'Humbug\Acme\RecordedClass'],
                    ['Acme\AnotherRecordedClass', 'Humbug\Acme\AnotherRecordedClass'],
                ],
                [
                    ['Acme\recorded_function', 'Humbug\Acme\recorded_function'],
                    ['Acme\another_recorded_function', 'Humbug\Acme\another_recorded_function'],
                ],
            ),
            new SymbolsRegistry(),
            $actualCode,
            <<<'TEXT'
                =============================================================
                SPECIFICATION
                =============================================================
                [Example of simple spec file] Spec with the more verbose form
                Fixtures/complete-spec-file.php

                =============================================================
                INPUT
                expose global classes: true
                expose global functions: true
                expose global constants: true

                exclude namespaces: [
                  - excludednamespace
                  - anotherexcludednamespace
                ]
                expose namespaces: [
                  - exposednamespace
                  - anotherexposednamespace
                ]

                expose classes: [
                  - exposedclass
                  - anotherexposedclass
                ]
                expose functions: [
                  - exposed_function
                  - another_exposed_function
                ]
                expose constants: [
                  - EXPOSED_CONSTANT
                  - ANOTHER_EXPOSED_CONSTANT
                ]

                (raw) internal classes: [
                  - excludedclass
                  - anotherexcludedclass
                ]
                (raw) internal functions: [
                  - excluded_function
                  - another_excluded_function
                ]
                (raw) internal constants: [
                  - EXCLUDED_CONSTANT
                  - ANOTHER_EXCLUDED_CONSTANT
                ]
                =============================================================
                echo "Hello world!";


                =============================================================
                EXPECTED
                =============================================================
                namespace Humbug;

                echo "Hello world!";

                ----------------
                recorded functions: [
                  - Acme\recorded_function => Humbug\Acme\recorded_function
                  - Acme\another_recorded_function => Humbug\Acme\another_recorded_function
                ]
                recorded classes: [
                  - Acme\RecordedClass => Humbug\Acme\RecordedClass
                  - Acme\AnotherRecordedClass => Humbug\Acme\AnotherRecordedClass
                ]

                =============================================================
                ACTUAL
                =============================================================
                namespace Prefix;

                echo "Hello world!";

                ----------------
                recorded functions: []
                recorded classes: []

                -------------------------------------------------------------------------------
                TEXT,
        ];

        yield 'simple scenario with recorded symbols' => [
            new SpecScenario(
                null,
                null,
                null,
                'Fixtures/complete-spec-file.php',
                '[Example of simple spec file] Spec with the more verbose form',
                $specCode,
                'Humbug',
                self::createSymbolsConfiguration([]),
                $expectedCode,
                [],
                [],
            ),
            SymbolsRegistry::create(
                [
                    ['recorded_function', 'Humbug\recorded_function'],
                    ['another_recorded_function', 'Humbug\another_recorded_function'],
                ],
                [
                    ['RecordedClass', 'Humbug\RecordedClass'],
                    ['AnotherRecordedClass', 'Humbug\AnotherRecordedClass'],
                ],
            ),
            $actualCode,
            <<<'TEXT'
                =============================================================
                SPECIFICATION
                =============================================================
                [Example of simple spec file] Spec with the more verbose form
                Fixtures/complete-spec-file.php

                =============================================================
                INPUT
                expose global classes: true
                expose global functions: true
                expose global constants: true

                exclude namespaces: []
                expose namespaces: []

                expose classes: []
                expose functions: []
                expose constants: []

                (raw) internal classes: []
                (raw) internal functions: []
                (raw) internal constants: []
                =============================================================
                echo "Hello world!";


                =============================================================
                EXPECTED
                =============================================================
                namespace Humbug;

                echo "Hello world!";

                ----------------
                recorded functions: []
                recorded classes: []

                =============================================================
                ACTUAL
                =============================================================
                namespace Prefix;

                echo "Hello world!";

                ----------------
                recorded functions: [
                  - recorded_function => Humbug\recorded_function
                  - another_recorded_function => Humbug\another_recorded_function
                ]
                recorded classes: [
                  - RecordedClass => Humbug\RecordedClass
                  - AnotherRecordedClass => Humbug\AnotherRecordedClass
                ]

                -------------------------------------------------------------------------------
                TEXT,
        ];
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
