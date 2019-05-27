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

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\ReflectorFactory;
use Humbug\PhpScoper\Scoper\FakeScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Whitelist;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Roave\BetterReflection\BetterReflection;

/**
 * @covers \Humbug\PhpScoper\PhpParser\TraverserFactory
 */
class TraverserFactoryTest extends TestCase
{
    public function test_creates_a_new_traverser_at_each_call(): void
    {
        $prefix = 'Humbug';

        $whitelist = Whitelist::create(true, true, true, 'Foo');

        $traverserFactory = new TraverserFactory(ReflectorFactory::create(null));

        $phpScoper = new PhpScoper(
            new FakeParser(),
            new FakeScoper(),
            (new ReflectionClass(TraverserFactory::class))->newInstanceWithoutConstructor()
        );

        $firstTraverser = $traverserFactory->create($phpScoper, $prefix, $whitelist);
        $secondTraverser = $traverserFactory->create($phpScoper, $prefix, $whitelist);

        $this->assertNotSame($firstTraverser, $secondTraverser);
    }
}
