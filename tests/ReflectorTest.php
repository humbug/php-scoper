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

/**
 * @covers \Humbug\PhpScoper\Reflector
 */
class ReflectorTest extends TestCase
{
    /**
     * @dataProvider provideClasses
     */
    public function test_it_can_identify_internal_classes(string $code, string $class, bool $expected): void
    {
        $reflector = ReflectorFactory::create($code);

        $actual = $reflector->isClassInternal($class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideFunctions
     */
    public function test_it_can_identify_internal_functions(string $code, string $class, bool $expected): void
    {
        $reflector = ReflectorFactory::create($code);

        $actual = $reflector->isFunctionInternal($class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideConstants
     */
    public function test_it_can_identify_internal_constants(string $code, string $class, bool $expected): void
    {
        $reflector = ReflectorFactory::create($code);

        $actual = $reflector->isConstantInternal($class);

        $this->assertSame($expected, $actual);
    }

    public function provideClasses(): Generator
    {
        yield 'PHP internal class' => [
            '<?php',
            'DateTime',
            true,
        ];

        yield 'PHP unknown user-defined class' => [
            '<?php',
            'Foo',
            false,
        ];

        yield 'PHP user-defined class with its declaration' => [
            <<<'PHP'
<?php

class Foo {}
PHP
            ,
            'Foo',
            false,
        ];

        // Stubs takes precedence: the real code would result in a error since the name is already taken
        yield 'PHP user-defined class overriding the internal class with its code declaration' => [
            <<<'PHP'
<?php

class DateTime {}
PHP
            ,
            'DateTime',
            true,
        ];

        yield 'PHP 7.0.0 new internal class' => [
            '<?php',
            'ReflectionGenerator',
            true,
        ];

        // No new class or interface in 7.1.0

        yield 'PHP 7.2.0 new internal class' => [
            '<?php',
            'Countable',
            true,
        ];

        yield 'PHP extension internal class' => [
            '<?php',
            'Redis',
            true,
        ];
    }

    public function provideFunctions(): Generator
    {
        yield 'PHP internal function' => [
            '<?php',
            'class_exists',
            true,
        ];

        yield 'PHP unknown user-defined function' => [
            '<?php',
            'unknown',
            false,
        ];

        yield 'PHP user-defined function with its declaration' => [
            <<<'PHP'
<?php

function foo() {}
PHP
            ,
            'foo',
            false,
        ];

        yield 'PHP 7.0.0 new internal function' => [
            '<?php',
            'error_clear_last',
            true,
        ];

        yield 'PHP 7.1.0 new internal function' => [
            '<?php',
            'is_iterable',
            true,
        ];

        yield 'PHP 7.2.0 new internal function' => [
            '<?php',
            'spl_object_id',
            true,
        ];

        yield 'PHP extension internal function' => [
            '<?php',
            'ftp_alloc',
            true,
        ];
    }

    public function provideConstants(): Generator
    {
        yield 'PHP internal constant' => [
            '<?php',
            'PHP_VERSION',
            true,
        ];

        yield 'PHP unknown user-defined constant' => [
            '<?php',
            'UNKNOWN',
            false,
        ];

        yield 'PHP user-defined constant with its code declaration' => [
            <<<'PHP'
<?php

const FOO = '';
PHP
            ,
            'FOO',
            false,
        ];

        // Stubs takes precedence: the real code would result in a error since the name is already taken
        yield 'PHP user-defined constant overriding the internal constant with its class declaration' => [
            <<<'PHP'
<?php

const PHP_VERSION = '';
PHP
            ,
            'PHP_VERSION',
            true,
        ];

        yield 'PHP 7.0.0 new internal constant' => [
            '<?php',
            'PHP_INT_MIN',
            true,
        ];

        yield 'PHP 7.1.0 new internal constant' => [
            '<?php',
            'CURLMOPT_PUSHFUNCTION',
            true,
        ];

        yield 'PHP 7.2.0 new internal constant' => [
            '<?php',
            'PHP_OS_FAMILY',
            true,
        ];

        yield 'PHP 7.3.0 new internal constant' => [
            '<?php',
            'JSON_THROW_ON_ERROR',
            true,
        ];

        // TODO: As of now, this is not give the expected result since constants are not taken from the stubs but from
        // the known & loaded constants.
        yield 'PHP extension internal constant' => [
            '<?php',
            'FTP_ASCII',
            false,
        ];
    }
}
