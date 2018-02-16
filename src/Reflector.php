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

use ReflectionException;
use ReflectionFunction;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Roave\BetterReflection\Reflector\FunctionReflector;

final class Reflector
{
    private $classReflector;
    private $functionReflector;

    public function __construct(ClassReflector $classReflector, FunctionReflector $functionReflector)
    {
        $this->classReflector = $classReflector;
        $this->functionReflector = $functionReflector;
    }

    public function isClassInternal(string $name): bool
    {
        try {
            return $this->classReflector->reflect($name)->isInternal();
        } catch (IdentifierNotFound $exception) {
            return false;
        }
    }

    public function isFunctionInternal(string $name): bool
    {
        try {
            return (new ReflectionFunction($name))->isInternal();

            // TODO: leverage Better Reflection instead
            return $this->functionReflector->reflect($name)->isInternal();
        } catch (ReflectionException $exception) {
            return false;
        }
    }
}
