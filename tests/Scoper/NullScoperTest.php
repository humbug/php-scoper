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
use Humbug\PhpScoper\Whitelist;
use PHPUnit\Framework\TestCase;
use function Humbug\PhpScoper\create_fake_patcher;
use function is_a;

/**
 * @covers \Humbug\PhpScoper\Scoper\NullScoper
 */
class NullScoperTest extends TestCase
{
    public function test_is_a_Scoper(): void
    {
        self::assertTrue(is_a(NullScoper::class, Scoper::class, true));
    }

    public function test_returns_the_file_content_unchanged(): void
    {
        $filePath = 'file';
        $contents = $expected = 'File content';
        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create();

        $scoper = new NullScoper();

        $actual = $scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        self::assertSame($expected, $actual);
    }
}
