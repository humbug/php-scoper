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

    public function test_it_gets_the_php_scoper_version(): void
    {
        $version = get_php_scoper_version();

        self::assertStringContainsString('@', $version);
    }

    /**
     * @dataProvider provideGenerators
     */
    public function test_it_can_chain_iterators(array $iterators, array $expected): void
    {
        $actual = iterator_to_array(chain(...$iterators), true);

        self::assertSame($expected, $actual);
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
                    yield 'a' => 'alpha';
                    yield 'b' => 'beta';
                    yield 2 => 'two';
                })(),
                (static function (): Generator {
                    yield 0;
                    yield 1;
                    yield 2;
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
