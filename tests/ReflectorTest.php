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
use PhpParser\Lexer\Emulative;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Ast\Parser\MemoizingParser;
use Roave\BetterReflection\SourceLocator\SourceStubber\AggregateSourceStubber;
use Roave\BetterReflection\SourceLocator\SourceStubber\PhpStormStubsSourceStubber;
use Roave\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;

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
        $reflector = $this->createReflector($code);

        $actual = $reflector->isClassInternal($class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideFunctions
     */
    public function test_it_can_identify_internal_functions(string $code, string $class, bool $expected): void
    {
        $reflector = $this->createReflector($code);

        $actual = $reflector->isFunctionInternal($class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideConstants
     */
    public function test_it_can_identify_internal_constants(string $code, string $class, bool $expected): void
    {
        $reflector = $this->createReflector($code);

        $actual = $reflector->isConstantInternal($class);

        $this->assertSame($expected, $actual);
    }

    public function provideClasses(): Generator
    {
        yield [
            '<?php',
            'DateTime',
            true,
        ];

        yield [
            '<?php',
            'Foo',
            false,
        ];

        yield [
            <<<'PHP'
<?php

class Foo {}
PHP
            ,
            'Foo',
            false,
        ];

        yield [
            <<<'PHP'
<?php

class DateTime {}
PHP
            ,
            'DateTime',
            false,
        ];

        yield [
            '<?php',
            'ReflectionGenerator',  // 7.0.0
            true,
        ];

        yield [
            '<?php',
            'Countable',    // 7.2.0
            true,
        ];
    }

    public function provideFunctions(): Generator
    {
//        yield [
//            '<?php',
//            'class_exists',
//            true,
//        ];
//
//        yield [
//            '<?php',
//            'unknown',
//            false,
//        ];
//
//        yield [
//            <<<'PHP'
//<?php
//
//function foo() {}
//PHP
//            ,
//            'foo',
//            false,
//        ];
//
//        yield [
//            '<?php',
//            'spl_object_id',  // PHP 7.2.0
//            true,
//        ];

        yield [
            '<?php',
            'is_countable',  // PHP 7.3.0
            true,
        ];
    }

    public function provideConstants(): Generator
    {
        yield [
            '<?php',
            'PHP_VERSION',
            true,
        ];

        yield [
            '<?php',
            'UNKNOWN',
            false,
        ];

        yield [
            <<<'PHP'
<?php

const FOO = '';
PHP
            ,
            'FOO',
            false,
        ];

        yield [
            <<<'PHP'
<?php

const PHP_VERSION = '';
PHP
            ,
            'PHP_VERSION',
            true,
        ];

        yield [
            '<?php',
            'PHP_OS_FAMILY',  // PHP 7.2.0
            true,
        ];

        yield [
            '<?php',
            'JSON_THROW_ON_ERROR',  // PHP 7.3.0
            true,
        ];
    }

    private function createReflector(string $code): Reflector
    {
        $astLocator = (new BetterReflection())->astLocator();

        $sourceLocator = new AggregateSourceLocator([
            new StringSourceLocator($code, $astLocator),
            new PhpInternalSourceLocator(
                $astLocator,
                new AggregateSourceStubber(
                    new PhpStormStubsSourceStubber(
                        new MemoizingParser(
                            (new ParserFactory())->create(
                                ParserFactory::PREFER_PHP7,
                                new Emulative([
                                    'usedAttributes' => [
                                        'comments',
                                        'startLine',
                                        'endLine',
                                        'startFilePos',
                                        'endFilePos',
                                    ],
                                ])
                            )
                        )
                    ),
                    new ReflectionSourceStubber()
                )
            ),
        ]);

        $classReflector = new ClassReflector($sourceLocator);

        $functionReflector = new FunctionReflector($sourceLocator, $classReflector);

        return new Reflector($classReflector, $functionReflector);
    }
}
