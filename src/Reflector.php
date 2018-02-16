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

use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;

final class Reflector
{
    private $classReflector;

    public function __construct(ClassReflector $classReflector)
    {
        $this->classReflector = $classReflector;
    }

    public function isClassInternal(string $name): bool
    {
        try {
            return $this->classReflector->reflect($name)->isInternal();
        } catch (IdentifierNotFound $exception) {
            return false;
        }
    }
}
