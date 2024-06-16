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

namespace Humbug\PhpScoper\Scoper\Factory;

use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Patcher\FakePatcher;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\FakePrinter;
use Humbug\PhpScoper\PhpParser\Parser\DummyParserFactory;
use Humbug\PhpScoper\PhpParser\Printer\DummyPrinterFactory;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(StandardScoperFactory::class)]
final class StandardScoperFactoryTest extends TestCase
{
    public function test_it_can_create_a_scoper(): void
    {
        $factory = new StandardScoperFactory(
            new EnrichedReflectorFactory(
                Reflector::createEmpty(),
            ),
            new DummyParserFactory(new FakeParser()),
            new DummyPrinterFactory(new FakePrinter()),
        );

        $factory->createScoper(
            new Configuration(
                null,
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
