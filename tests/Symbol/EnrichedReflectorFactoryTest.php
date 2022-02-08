<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Symbol\EnrichedReflectorFactory
 */
final class EnrichedReflectorFactoryTest extends TestCase
{
    public function test_it_can_create_an_enriched_reflector(): void
    {
        // TODO: named param could help here
        $symbolsConfiguration = SymbolsConfiguration::create(
            false,
            false,
            false,
            null,
            null,
            null,
            null,
            null,
            ['Acme\Foo'],
            ['Acme\main'],
            ['Acme\BAR'],
        );

        $factory = new EnrichedReflectorFactory(Reflector::createEmpty());

        $expected = new EnrichedReflector(
            Reflector::createEmpty()->withSymbols(
                ['Acme\Foo'],
                ['Acme\main'],
                ['Acme\BAR'],
            ),
            $symbolsConfiguration,
        );

        $actual = $factory->create($symbolsConfiguration);

        self::assertEquals($expected, $actual);
    }
}
