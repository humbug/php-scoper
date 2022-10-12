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

use ArrayIterator;
use Generator;
use Humbug\PhpScoper\Console\Application;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

/**
 * @internal
 */
class FunctionsTest extends TestCase
{
    public function test_it_can_create_an_application(): void
    {
        $app1 = Application::create();
        $app2 = Application::create();

        self::assertNotSame($app1, $app2);
    }

    public function test_it_gets_the__ph_p__scoper_version(): void
    {
        $version = get_php_scoper_version();

        self::assertStringContainsString('@', $version);
    }

    /**
     * @dataProvider providePaths
     */
    public function test_get_the_common_path(array $paths, string $expected): void
    {
        $actual = get_common_path($paths);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideGenerators
     */
    public function test_it_can_chain_iterators(array $iterators, array $expected): void
    {
        $actual = iterator_to_array(chain(...$iterators), true);

        self::assertSame($expected, $actual);
    }

    public static function providePaths(): iterable
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

        yield [
            [
                'C:\\path\\to\\file',
            ],
            'C:\\path\\to',
        ];

        yield [
            [
                'C:\\path\\to\\file',
                'C:\\path\\to\\another-file',
            ],
            'C:\\path\\to',
        ];

        yield [
            [
                'C:\\path\\to\\file',
                'C:\\path\\to\\another-file',
                'C:\\path\\another-to\\another-file',
            ],
            'C:\\path',
        ];

        yield [
            [
                'C:\\path\\to\\file',
                'D:\\another\\path\\to\\another-file',
            ],
            '',
        ];
    }

    public static function provideGenerators(): iterable
    {
        yield [
            [],
            [],
        ];

        yield [
            [
                ['a' => 'alpha', 'b' => 'beta', 2 => 'two'],
                [0, 1, 2],
            ],
            [
                'a' => 'alpha',
                'b' => 'beta',
                2 => 2,
                0 => 0,
                1 => 1,
            ],
        ];

        yield [
            [
                new ArrayIterator(['a' => 'alpha', 'b' => 'beta', 2 => 'two']),
                new ArrayIterator([0, 1, 2]),
            ],
            [
                'a' => 'alpha',
                'b' => 'beta',
                2 => 2,
                0 => 0,
                1 => 1,
            ],
        ];

        yield [
            [
                (static function (): Generator {
                    yield from ['a' => 'alpha', 'b' => 'beta', 2 => 'two'];
                })(),
                (static function (): Generator {
                    yield from [0, 1, 2];
                })(),
            ],
            [
                'a' => 'alpha',
                'b' => 'beta',
                2 => 2,
                0 => 0,
                1 => 1,
            ],
        ];
    }
}
