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
use Roave\BetterReflection\Identifier\Exception\InvalidIdentifierName;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Roave\BetterReflection\Reflector\FunctionReflector;
use function array_key_exists;
use function array_values;
use function get_defined_constants;
use function strtoupper;

final class Reflector
{
    private $classReflector;
    private $functionReflector;
    private $constants;

    public function __construct(ClassReflector $classReflector, FunctionReflector $functionReflector)
    {
        $this->classReflector = $classReflector;
        $this->functionReflector = $functionReflector;
    }

    public function isClassInternal(string $name): bool
    {
        try {
            return $this->classReflector->reflect($name)->isInternal();
        } catch (IdentifierNotFound|InvalidIdentifierName $exception) {
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

    public function isConstantInternal(string $name): bool
    {
        if (null === $this->constants) {
            $constants = get_defined_constants(true);

            unset($constants['user']);

            $this->constants = array_merge(...array_values($constants));

            unset($constants);
        }

        // TODO: find a better solution
        return array_key_exists(strtoupper($name), $this->constants);
    }
}
