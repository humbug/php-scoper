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

namespace Humbug\PhpScoper\PhpParser\Node;

use PhpParser\Node\Name;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NameFactory::class)]
final class NameFactoryTest extends TestCase
{
    #[DataProvider('nameProvider')]
    public function test_it_can_concatenate_two_strings(
        string|array|Name|null $name1,
        string|array|Name|null $name2,
        ?array $attributes,
        Name $expected,
    ): void {
        $actual = NameFactory::concat(
            $name1,
            $name2,
            $attributes,
        );

        self::assertEquals($expected, $actual);
    }

    public static function nameProvider(): iterable
    {
        $attributes = ['position' => 4];
        $firstNameAttributes = ['token' => 3];
        $secondNameAttributes = ['space' => 2];

        yield 'both strings; no attributes' => [
            'Prefix',
            'main',
            null,
            new Name(
                'Prefix\main',
                [],
            ),
        ];

        yield 'both strings; with attributes' => [
            'Prefix',
            'main',
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'both array of strings; no attributes' => [
            ['Prefix', 'Humbug'],
            ['Acme', 'main'],
            null,
            new Name(
                'Prefix\Humbug\Acme\main',
                [],
            ),
        ];

        yield 'both array of strings; with attributes' => [
            ['Prefix', 'Humbug'],
            ['Acme', 'main'],
            $attributes,
            new Name(
                'Prefix\Humbug\Acme\main',
                $attributes,
            ),
        ];

        yield 'two names; no attributes' => [
            new Name('Prefix'),
            new Name('main'),
            null,
            new Name(
                'Prefix\main',
                [],
            ),
        ];

        yield 'two names; with attributes' => [
            new Name('Prefix'),
            new Name('main'),
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'two names; both with attributes and explicit attributes: explicit attributes take over' => [
            new Name('Prefix', $firstNameAttributes),
            new Name('main', $secondNameAttributes),
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'two names; both with attributes and no explicit attributes: the second name attributes are taken' => [
            new Name('Prefix', $firstNameAttributes),
            new Name('main', $secondNameAttributes),
            null,
            new Name(
                'Prefix\main',
                $secondNameAttributes,
            ),
        ];

        yield 'two names; only the first name has attributes: the second name attributes are taken' => [
            new Name('Prefix', $firstNameAttributes),
            new Name('main'),
            null,
            new Name(
                'Prefix\main',
                [],
            ),
        ];

        yield 'two names; only the second name has attributes: the second name attributes are taken' => [
            new Name('Prefix'),
            new Name('main', $secondNameAttributes),
            null,
            new Name(
                'Prefix\main',
                $secondNameAttributes,
            ),
        ];

        yield 'only one name; name + string; no attributes' => [
            new Name('Prefix'),
            'main',
            null,
            new Name(
                'Prefix\main',
                [],
            ),
        ];

        yield 'only one name; name + string; explicit attributes' => [
            new Name('Prefix'),
            'main',
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'only one name; name with attributes + string; explicit attributes' => [
            new Name('Prefix', $firstNameAttributes),
            'main',
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'only one name; name with attributes + string; no explicit attributes' => [
            new Name('Prefix', $firstNameAttributes),
            'main',
            null,
            new Name(
                'Prefix\main',
                $firstNameAttributes,
            ),
        ];

        yield 'only one name; string + name; no attributes' => [
            'Prefix',
            new Name('main'),
            null,
            new Name(
                'Prefix\main',
                [],
            ),
        ];

        yield 'only one name; string + name; explicit attributes' => [
            'Prefix',
            new Name('main'),
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'only one name; string + name with attributes; explicit attributes' => [
            'Prefix',
            new Name('main', $secondNameAttributes),
            $attributes,
            new Name(
                'Prefix\main',
                $attributes,
            ),
        ];

        yield 'only one name; string + name with attributes; no explicit attributes' => [
            'Prefix',
            new Name('main', $secondNameAttributes),
            null,
            new Name(
                'Prefix\main',
                $secondNameAttributes,
            ),
        ];
    }
}
