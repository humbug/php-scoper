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

use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Patcher\FakePatcher;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\FakePrinter;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ScoperFactory::class)]
final class ScoperFactoryTest extends TestCase
{
    public function test_it_can_create_a_scoper(): void
    {
        $factory = new ScoperFactory(
            new FakeParser(),
            new EnrichedReflectorFactory(
                Reflector::createEmpty(),
            ),
            new FakePrinter(),
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
