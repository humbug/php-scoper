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

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\SpecFramework\SpecScenario;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\SpecFramework\SpecScenario
 * @internal
 */
final class SpecScenarioTest extends TestCase
{
    #[DataProvider('phpVersionProvider')]
    public function test_it_can_get_the_php_version_used(
        SpecScenario $scenario,
        ?PhpVersion $expected,
    ): void {
        $actual = $scenario->getPhpParserVersion();

        self::assertEquals($expected, $actual);
    }

    public static function phpVersionProvider(): iterable
    {
        yield 'no PHP version' => [
            new SpecScenario(
                minPhpVersion: null,
                maxPhpVersion: null,
                phpVersionUsed: null,
                file: '',
                title: '',
                inputCode: '',
                prefix: '',
                symbolsConfiguration: SymbolsConfiguration::create(),
                expectedCode: '',
                expectedRegisteredClasses: [],
                expectedRegisteredFunctions: [],
            ),
            null,
        ];

        yield 'specific PHP version' => [
            new SpecScenario(
                minPhpVersion: null,
                maxPhpVersion: null,
                phpVersionUsed: 80_200,
                file: '',
                title: '',
                inputCode: '',
                prefix: '',
                symbolsConfiguration: SymbolsConfiguration::create(),
                expectedCode: '',
                expectedRegisteredClasses: [],
                expectedRegisteredFunctions: [],
            ),
            PhpVersion::fromString('8.2'),
        ];
    }
}
