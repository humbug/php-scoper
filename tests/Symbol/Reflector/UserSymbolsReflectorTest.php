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

/**
 * @covers \Humbug\PhpScoper\Symbol\Reflector
 */
class UserSymbolsReflectorTest extends TestCase
{
    /**
     * @dataProvider symbolsProvider
     */
    public function test_it_can_be_enriched_with_arbitrary_symbols(
        array $classNames,
        array $functionNames,
        array $constantNames
    ): void
    {
        $reflector = Reflector::createEmpty()->withAdditionalSymbols(
            $classNames,
            $functionNames,
            $constantNames,
        );
        
        foreach ($classNames as $className) {
            self::assertTrue($reflector->isClassInternal($className));
        }

        foreach ($functionNames as $functionName) {
            self::assertTrue($reflector->isFunctionInternal($functionName));
        }

        foreach ($constantNames as $constantName) {
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

        $reflectorWithA = $emptyReflector->withAdditionalSymbols([$classA], [], []);

        self::assertFalse($emptyReflector->isClassInternal($classA));
        self::assertFalse($emptyReflector->isClassInternal($classB));
        self::assertTrue($reflectorWithA->isClassInternal($classA));
        self::assertFalse($reflectorWithA->isClassInternal($classB));

        $reflectorWithAandB = $reflectorWithA->withAdditionalSymbols([$classB], [], []);

        self::assertFalse($emptyReflector->isClassInternal($classA));
        self::assertFalse($emptyReflector->isClassInternal($classB));
        self::assertTrue($reflectorWithA->isClassInternal($classA));
        self::assertFalse($reflectorWithA->isClassInternal($classB));
        self::assertTrue($reflectorWithAandB->isClassInternal($classA));
        self::assertTrue($reflectorWithAandB->isClassInternal($classB));
    }

    public static function symbolsProvider(): iterable
    {
        $classNames = ['PHPUnit\Framework\TestCase', 'Symfony\Component\Finder\Finder'];
        $functionNames = ['PHPUnit\main', 'Symfony\dump'];
        $constantNames = ['PHPUnit\VERSION', 'Symfony\VERSION'];

        yield 'classes' => [
            $classNames,
            [],
            [],
        ];

        yield 'functions' => [
            [],
            $functionNames,
            [],
        ];

        yield 'constants' => [
            [],
            [],
            $constantNames,
        ];

        yield 'nominal' => [
            $classNames,
            $functionNames,
            $constantNames,
        ];
    }
}
