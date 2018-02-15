<?php

declare(strict_types=1);

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