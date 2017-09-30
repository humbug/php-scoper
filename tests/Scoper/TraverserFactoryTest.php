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

use Humbug\PhpScoper\Scoper;
use PHPUnit\Framework\TestCase;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_fake_whitelister;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;

/**
 * @covers \Humbug\PhpScoper\Scoper\TraverserFactory
 */
class TraverserFactoryTest extends TestCase
{
    public function test_creates_a_new_traverser_at_each_call()
    {
        $prefix = 'Humbug';

        $whitelist = ['Foo'];

        $whitelister = create_fake_whitelister();

        $traverserFactory = new TraverserFactory();

        $firstTraverser = $traverserFactory->create($prefix, $whitelist, $whitelister);
        $secondTraverser = $traverserFactory->create($prefix, $whitelist, $whitelister);

        $this->assertNotSame($firstTraverser, $secondTraverser);
    }
}
