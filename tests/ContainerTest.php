<?php

declare(strict_types=1);

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

        $this->assertNotNull($result);
    }

    /**
     * @dataProvider provideServiceGetter
     */
    public function test_it_always_returns_the_same_instance_on_a_container_basis(string $getterName): void
    {
        $container = new Container();
        $anotherContainer = new Container();

        $this->assertSame(
            $container->$getterName(),
            $container->$getterName()
        );

        $this->assertNotSame(
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
