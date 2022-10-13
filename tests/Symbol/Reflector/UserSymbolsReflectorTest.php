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
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * @covers \Humbug\PhpScoper\Symbol\Reflector
 *
 * @internal
 */
class UserSymbolsReflectorTest extends TestCase
{
    /**
     * @dataProvider symbolsProvider
     */
    public function test_it_can_be_enriched_with_arbitrary_symbols(
        SymbolRegistry $classes,
        SymbolRegistry $functions,
        SymbolRegistry $constants
    ): void {
        $reflector = Reflector::createEmpty()->withAdditionalSymbols(
            $classes,
            $functions,
            $constants,
        );

        foreach ($classes->getNames() as $className) {
            self::assertTrue($reflector->isClassInternal($className));
        }

        foreach ($functions->getNames() as $functionName) {
            self::assertTrue($reflector->isFunctionInternal($functionName));
        }

        foreach ($constants->getNames() as $constantName) {
            self::assertTrue($reflector->isConstantInternal($constantName));
        }
    }

    public function test_it_can_be_enriched_multiple_times(): void
    {
        $classA = 'Acme\A';
        $classB = 'Acme\B';

        $emptyReflector = Reflector::createEmpty();

        // Sanity check
        self::assertFalse($emptyReflector->isClassInternal($classA));
        self::assertFalse($emptyReflector->isClassInternal($classB));

        $reflectorWithA = $emptyReflector->withAdditionalSymbols(
            SymbolRegistry::create([$classA]),
            SymbolRegistry::create(),
            SymbolRegistry::createForConstants(),
        );

        self::assertFalse($emptyReflector->isClassInternal($classA));
        self::assertFalse($emptyReflector->isClassInternal($classB));
        self::assertTrue($reflectorWithA->isClassInternal($classA));
        self::assertFalse($reflectorWithA->isClassInternal($classB));

        $reflectorWithAandB = $reflectorWithA->withAdditionalSymbols(
            SymbolRegistry::create([$classB]),
            SymbolRegistry::create(),
            SymbolRegistry::createForConstants(),
        );

        self::assertFalse($emptyReflector->isClassInternal($classA));
        self::assertFalse($emptyReflector->isClassInternal($classB));
        self::assertTrue($reflectorWithA->isClassInternal($classA));
        self::assertFalse($reflectorWithA->isClassInternal($classB));
        self::assertTrue($reflectorWithAandB->isClassInternal($classA));
        self::assertTrue($reflectorWithAandB->isClassInternal($classB));
    }

    public static function symbolsProvider(): iterable
    {
        $classNames = SymbolRegistry::create([
            TestCase::class,
            Finder::class,
        ]);
        $functionNames = SymbolRegistry::create([
            'PHPUnit\main',
            'Symfony\dump',
        ]);
        $constantNames = SymbolRegistry::createForConstants([
            'PHPUnit\VERSION',
            'Symfony\VERSION',
        ]);

        yield 'classes' => [
            $classNames,
            SymbolRegistry::create(),
            SymbolRegistry::createForConstants(),
        ];

        yield 'functions' => [
            SymbolRegistry::create(),
            $functionNames,
            SymbolRegistry::createForConstants(),
        ];

        yield 'constants' => [
            SymbolRegistry::create(),
            SymbolRegistry::create(),
            $constantNames,
        ];

        yield 'nominal' => [
            $classNames,
            $functionNames,
            $constantNames,
        ];
    }
}
