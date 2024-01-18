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

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(\Humbug\PhpScoper\Symbol\EnrichedReflectorFactory::class)]
final class EnrichedReflectorFactoryTest extends TestCase
{
    public function test_it_can_create_an_enriched_reflector(): void
    {
        $symbolsConfiguration = SymbolsConfiguration::create(
            excludedClasses: SymbolRegistry::create(['Acme\Foo']),
            excludedFunctions: SymbolRegistry::create(['Acme\main']),
            excludedConstants: SymbolRegistry::createForConstants(['Acme\BAR']),
        );

        $factory = new EnrichedReflectorFactory(Reflector::createEmpty());

        $expected = new EnrichedReflector(
            Reflector::createEmpty()->withAdditionalSymbols(
                SymbolRegistry::create(['Acme\Foo']),
                SymbolRegistry::create(['Acme\main']),
                SymbolRegistry::createForConstants(['Acme\BAR']),
            ),
            $symbolsConfiguration,
        );

        $actual = $factory->create($symbolsConfiguration);

        self::assertEquals($expected, $actual);
    }
}
