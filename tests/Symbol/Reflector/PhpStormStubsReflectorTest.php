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

namespace Humbug\PhpScoper\Symbol\Reflector;

use Humbug\PhpScoper\Symbol\Reflector;
use PHPUnit\Framework\TestCase;
use function event_add;
use function event_base_reinit;

/**
 * @covers \Humbug\PhpScoper\Symbol\Reflector
 *
 * @internal
 */
class PhpStormStubsReflectorTest extends TestCase
{
    private Reflector $reflector;

    protected function setUp(): void
    {
        $this->reflector = Reflector::createWithPhpStormStubs();
    }

    /**
     * @dataProvider provideClasses
     */
    public function test_it_can_identify_internal_classes(string $class, bool $expected): void
    {
        $actual = $this->reflector->isClassInternal($class);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideFunctions
     */
    public function test_it_can_identify_internal_functions(string $class, bool $expected): void
    {
        $actual = $this->reflector->isFunctionInternal($class);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideConstants
     */
    public function test_it_can_identify_internal_constants(string $class, bool $expected): void
    {
        $actual = $this->reflector->isConstantInternal($class);

        self::assertSame($expected, $actual);
    }

    public static function provideClasses(): iterable
    {
        yield 'PHP internal class' => [
            'DateTime',
            true,
        ];

        yield 'FQ PHP internal class' => [
            '\DateTime',
            true,
        ];

        yield 'PHP unknown user-defined class' => [
            'Foo',
            false,
        ];

        yield 'PHP 7.0.0 new internal class' => [
            'ReflectionGenerator',
            true,
        ];

        // No new class or interface in 7.1.0

        yield 'PHP 7.2.0 new internal class' => [
            'Countable',
            true,
        ];

        yield 'PHP extension internal class' => [
            'Redis',
            true,
        ];
    }

    public static function provideFunctions(): iterable
    {
        yield 'PHP internal function' => [
            'class_exists',
            true,
        ];

        yield 'FQ PHP internal function' => [
            '\class_exists',
            true,
        ];

        yield 'PHP internal function with the wrong case' => [
            'CLASS_EXISTS',
            true,
        ];

        yield 'PHP unknown user-defined function' => [
            'unknown',
            false,
        ];

        yield 'PHP 7.0.0 new internal function' => [
            'error_clear_last',
            true,
        ];

        yield 'PHP 7.1.0 new internal function' => [
            'is_iterable',
            true,
        ];

        yield 'PHP 7.2.0 new internal function' => [
            'spl_object_id',
            true,
        ];

        yield 'PHP extension internal function' => [
            'ftp_alloc',
            true,
        ];

        // https://github.com/sebastianbergmann/phpunit/issues/4211
        yield 'PHPDBG internal function' => [
            'phpdbg_break_file',
            true,
            event_add()
        ];
    }

    public static function provideConstants(): iterable
    {
        yield 'PHP internal constant' => [
            'PHP_VERSION',
            true,
        ];

        yield '\PHP internal constant' => [
            'PHP_VERSION',
            true,
        ];

        yield 'PHP unknown user-defined constant' => [
            'UNKNOWN',
            false,
        ];

        yield 'PHP 7.0.0 new internal constant' => [
            'PHP_INT_MIN',
            true,
        ];

        yield 'PHP 7.1.0 new internal constant' => [
            'CURLMOPT_PUSHFUNCTION',
            true,
        ];

        yield 'PHP 7.2.0 new internal constant' => [
            'PHP_OS_FAMILY',
            true,
        ];

        yield 'PHP 7.3.0 new internal constant' => [
            'JSON_THROW_ON_ERROR',
            true,
        ];

        yield 'PHP extension internal constant' => [
            'FTP_ASCII',
            true,
        ];
    }
}
