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

use InvalidArgumentException;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    #[DataProvider('provideServiceGetter')]
    public function test_it_can_instantiate_its_services(string $getterName): void
    {
        $result = (new Container())->{$getterName}();

        self::assertNotNull($result);
    }

    #[DataProvider('provideServiceGetter')]
    public function test_it_always_returns_the_same_instance_on_a_container_basis(string $getterName): void
    {
        $container = new Container();
        $anotherContainer = new Container();

        self::assertSame(
            $container->{$getterName}(),
            $container->{$getterName}(),
        );

        self::assertNotSame(
            $container->{$getterName}(),
            $anotherContainer->{$getterName}(),
        );
    }

    public static function provideServiceGetter(): iterable
    {
        foreach ((new ReflectionClass(Container::class))->getMethods() as $methodReflection) {
            if ($methodReflection->isPublic()) {
                yield [$methodReflection->getName()];
            }
        }
    }

    #[DataProvider('samePhpVersionProvider')]
    public function test_it_can_get_the_parser_if_the_version_does_not_change(
        ?PhpVersion $version1,
        ?PhpVersion $version2,
    ): void {
        $container = new Container();

        $container->getParser($version1);
        $container->getParser($version2);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('samePhpVersionProvider')]
    public function test_it_can_get_the_printer_if_the_version_does_not_change(
        ?PhpVersion $version1,
        ?PhpVersion $version2,
    ): void {
        $container = new Container();

        $container->getPrinter($version1);
        $container->getPrinter($version2);

        $this->addToAssertionCount(1);
    }

    public static function samePhpVersionProvider(): iterable
    {
        yield 'no PHP version configured' => [
            null,
            null,
        ];

        $phpVersion = PhpVersion::fromString('7.2');

        yield 'same PHP version instance' => [
            $phpVersion,
            $phpVersion,
        ];

        yield 'same PHP version, different instances' => [
            PhpVersion::fromString('7.3'),
            PhpVersion::fromString('7.3'),
        ];
    }

    #[DataProvider('differentPhpVersionProvider')]
    public function test_it_cannot_create_two_different_versions_of_the_parser(
        ?PhpVersion $version1,
        ?PhpVersion $version2,
    ): void {
        $container = new Container();

        $container->getParser($version1);

        $this->expectException(InvalidArgumentException::class);

        $container->getParser($version2);
    }

    #[DataProvider('differentPhpVersionProvider')]
    public function test_it_cannot_create_two_different_versions_of_the_printer(
        ?PhpVersion $version1,
        ?PhpVersion $version2,
    ): void {
        $container = new Container();

        $container->getPrinter($version1);

        $this->expectException(InvalidArgumentException::class);

        $container->getPrinter($version2);
    }

    public static function differentPhpVersionProvider(): iterable
    {
        $phpVersion = PhpVersion::fromString('7.2');
        $anotherPhpVersion = PhpVersion::fromString('7.3');

        yield 'no PHP version configured' => [
            null,
            $phpVersion,
        ];

        yield 'no PHP version requested' => [
            $phpVersion,
            null,
        ];

        yield 'different PHP versions' => [
            $phpVersion,
            $anotherPhpVersion,
        ];
    }
}
