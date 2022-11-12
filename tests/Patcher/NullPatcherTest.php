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

namespace Humbug\PhpScoper\Patcher;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Patcher\NullPatcher
 *
 * @internal
 */
final class NullPatcherTest extends TestCase
{
    public function test_it_returns_the_contents_unchanged(): void
    {
        $filePath = 'file.php';
        $contents = 'file contents';
        $prefix = '_Humbug';

        $patcher = new NullPatcher();

        self::assertSame($contents, $patcher($filePath, $prefix, $contents));
    }
}
