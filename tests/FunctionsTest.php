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

namespace Humbug\PhpScoper;

use Generator;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider providePaths
     */
    public function test_get_the_common_path(array $paths, string $expected): void
    {
        $actual = get_common_path($paths);

        $this->assertSame($expected, $actual);
    }

    public function providePaths(): Generator
    {
        yield [
            [],
            '',
        ];

        yield [
            [
                '/path/to/file',
            ],
            '/path/to',
        ];

        yield [
            [
                '/path/to/file',
                '/path/to/another-file',
            ],
            '/path/to',
        ];

        yield [
            [
                '/path/to/file',
                '/path/to/another-file',
                '/path/another-to/another-file',
            ],
            '/path',
        ];

        yield [
            [
                '/path/to/file',
                '/another/path/to/another-file',
            ],
            '',
        ];

        yield [
            [
                '/file',
            ],
            '',
        ];
    }
}
