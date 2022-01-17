<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Reflector;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use function strpos;

final class EnrichedReflector
{
    private SymbolsConfiguration $configuration;
    private Reflector $reflector;

    public function __construct(
        SymbolsConfiguration $configuration,
        Reflector $reflector
    ) {
        $this->configuration = $configuration;
        $this->reflector = $reflector;
    }

    public function belongsToExcludedNamespace(string $name): bool
    {
        return $this->configuration->getExcludedNamespaces()->belongsToRegisteredNamespace($name);
    }

    public function isExcludedNamespace(string $name): bool
    {
        return $this->configuration->getExcludedNamespaces()->isRegisteredNamespace($name);
    }

    public function belongsToExposedNamespace(string $name): bool
    {
        return $this->configuration->getExposedNamespaces()->belongsToRegisteredNamespace($name);
    }

    public function isExposedNamespace(string $name): bool
    {
        return $this->configuration->getExposedNamespaces()->isRegisteredNamespace($name);
    }

    public function isExposedFunctionFromGlobalNamespace(string $functionName): bool
    {
        return $this->configuration->shouldExposeGlobalFunctions() && false === strpos($functionName, '\\');
    }

    public function isExposedConstantFromGlobalNamespace(string $constantName): bool
    {
        return $this->configuration->shouldExposeGlobalConstants() && false === strpos($constantName, '\\');
    }

    public function isExposedClassFromGlobalNamespace(string $className): bool
    {
        return $this->configuration->shouldExposeGlobalClasses() && false === strpos($className, '\\');
    }

    public function isInternalFunction(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name);
    }

    public function isExposedFunction(FullyQualified $name): bool
    {
        $nameString = (string) $name;

        return (
            !$this->reflector->isFunctionInternal($nameString)
            && (
                $this->isExposedFunctionFromGlobalNamespace($nameString)
                || $this->isSymbolExposed($nameString)
            )
        );
    }

    public function isInternalClass(string $name): bool
    {
        return $this->reflector->isClassInternal($name);
    }

    public function isExposedClass(FullyQualified $name): bool
    {
        $nameString = (string) $name;

        return $this->isExposedClassFromGlobalNamespace($nameString)
            || $this->isSymbolExposed($nameString);
    }

    public function isInternalConstant(string $name): bool
    {
        return $this->reflector->isConstantInternal($name);
    }

    public function isExposedConstant(string $name): bool
    {
        return !$this->reflector->isConstantInternal($name)
            && $this->isSymbolExposed($name, true)
            && $this->isExposedConstantFromGlobalNamespace($name);
    }

    public function isSymbolExposed(string $name, bool $constant = false): bool
    {
        return true;
    }
}
