<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Patcher\FakePatcher;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Scoper\ScoperFactory
 */
final class ScoperFactoryTest extends TestCase
{
    public function test_it_can_create_a_scoper(): void
    {
        $factory = new ScoperFactory(
            new FakeParser(),
            new EnrichedReflectorFactory(
                Reflector::createEmpty(),
            ),
        );

        $factory->createScoper(
            new Configuration(
                null,
                '_Humbug',
                [],
                [],
                new FakePatcher(),
                SymbolsConfiguration::create(),
            ),
            new SymbolsRegistry(),
        );

        $this->addToAssertionCount(1);
    }
}
