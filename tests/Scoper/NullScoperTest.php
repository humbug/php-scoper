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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NullScoper::class)]
class NullScoperTest extends TestCase
{
    public function test_returns_the_file_content_unchanged(): void
    {
        $filePath = 'file';
        $contents = $expected = 'File content';

        $scoper = new NullScoper();

        $actual = $scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }
}
