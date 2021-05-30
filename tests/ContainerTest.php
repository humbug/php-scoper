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
use ReflectionClass;

/**
 * @covers \Humbug\PhpScoper\Container
 */
class ContainerTest extends TestCase
{
    /**
     * @dataProvider provideServiceGetter
     */
    public function test_it_can_instantiate_its_services(string $getterName): void
    {
        $result = (new Container())->$getterName();

        self::assertNotNull($result);
    }

    /**
     * @dataProvider provideServiceGetter
     */
    public function test_it_always_returns_the_same_instance_on_a_container_basis(string $getterName): void
    {
        $container = new Container();
        $anotherContainer = new Container();

        self::assertSame(
            $container->$getterName(),
            $container->$getterName()
        );

        self::assertNotSame(
            $container->$getterName(),
            $anotherContainer->$getterName()
        );
    }

    public function provideServiceGetter(): Generator
    {
        foreach ((new ReflectionClass(Container::class))->getMethods() as $methodReflection) {
            yield [$methodReflection->getName()];
        }
    }
}
