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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function str_replace;

/**
 * @covers \Humbug\PhpScoper\PhpParser\Node\FullyQualifiedFactory
 * @internal
 */
final class FullyQualifiedFactoryTest extends TestCase
{
    #[DataProvider('fullyQualifiedNameProvider')]
    public function test_it_can_concatenate_two_strings(
        string|array|Name|null $name1,
        string|array|Name|null $name2,
        ?array $attributes,
        Name $expected,
    ): void {
        $actual = FullyQualifiedFactory::concat(
            $name1,
            $name2,
            $attributes,
        );

        self::assertEquals($expected, $actual);
    }

    public static function fullyQualifiedNameProvider(): iterable
    {
        foreach (NameFactoryTest::nameProvider() as $label => [$name1, $name2, $attributes, $expected]) {
            /** @var Name $expected */
            $expected = self::toFullyQualified($expected);

            yield $label => [$name1, $name2, $attributes, $expected];

            if ($name1 instanceof Name || $name2 instanceof Name) {
                yield str_replace('name', 'full-qualified name', $label) => [
                    $name1 instanceof Name
                        ? self::toFullyQualified($name1)
                        : $name1,
                    $name2 instanceof Name
                        ? self::toFullyQualified($name2)
                        : $name2,
                    $attributes,
                    $expected,
                ];
            }
        }
    }

    private static function toFullyQualified(Name $name): Name\FullyQualified
    {
        return new Name\FullyQualified(
            $name->toString(),
            $name->getAttributes(),
        );
    }
}
