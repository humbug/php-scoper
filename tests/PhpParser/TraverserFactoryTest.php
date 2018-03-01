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
use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;

/**
 * @covers \Humbug\PhpScoper\PhpParser\TraverserFactory
 */
class TraverserFactoryTest extends TestCase
{
    public function test_creates_a_new_traverser_at_each_call()
    {
        $prefix = 'Humbug';

        $whitelist = ['Foo'];

        $classReflector = new Reflector(
            (new BetterReflection())->classReflector(),
            (new BetterReflection())->functionReflector()
        );

        $traverserFactory = new TraverserFactory($classReflector);

        $firstTraverser = $traverserFactory->create($prefix, $whitelist);
        $secondTraverser = $traverserFactory->create($prefix, $whitelist);

        $this->assertNotSame($firstTraverser, $secondTraverser);
    }
}
