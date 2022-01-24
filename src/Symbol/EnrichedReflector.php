<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Reflector;
use function strpos;

/**
 * Combines the API or the "traditional" reflector which is about to tell
 * if a symbol is internal or not with the more PHP-Scoper specific exposed
 * API.
 */
final class EnrichedReflector
{
    private Reflector $reflector;
    private SymbolsConfiguration $symbolsConfiguration;

    public function __construct(
        Reflector $reflector,
        SymbolsConfiguration $symbolsConfiguration
    ) {
        $this->reflector = $reflector;
        $this->symbolsConfiguration = $symbolsConfiguration;
    }

    public function belongsToExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->belongsToRegisteredNamespace($name);
    }

    public function isFunctionInternal(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name);
    }

    public function isFunctionExcluded(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name)
            || $this->belongsToExcludedNamespace($name);
    }

    public function isClassInternal(string $name): bool
    {
        return $this->reflector->isClassInternal($name);
    }

    public function isClassExcluded(string $name): bool
    {
        return $this->reflector->isClassInternal($name)
            || $this->belongsToExcludedNamespace($name);
    }

    public function isConstantInternal(string $name): bool
    {
        return $this->reflector->isConstantInternal($name);
    }

    public function isConstantExcluded(string $name): bool
    {
        // TODO: double check not sure that internal should mean excluded for constants
        return $this->reflector->isConstantInternal($name)
            || $this->belongsToExcludedNamespace($name);
    }

    public function isExposedFunction(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isFunctionInternal($resolvedName)
            && (
                $this->_isExposedFunctionFromGlobalNamespace($resolvedName)
                || $this->symbolsConfiguration
                        ->getExposedFunctions()
                        ->matches($resolvedName)
            );
    }

    public function isExposedFunctionFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isFunctionInternal($resolvedName)
            && $this->_isExposedFunctionFromGlobalNamespace($resolvedName);
    }

    public function isExposedClass(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isClassInternal($resolvedName)
            && (
                $this->_isExposedClassFromGlobalNamespace($resolvedName)
                || $this->symbolsConfiguration
                    ->getExposedClasses()
                    ->matches($resolvedName)
            );
    }

    public function isExposedClassFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isClassInternal($resolvedName)
            && $this->_isExposedClassFromGlobalNamespace($resolvedName);
    }

    public function isExposedConstant(string $name): bool
    {
        // Special case: internal constants must be treated as exposed symbols.
        //
        // Example: when declaring a new internal constant for compatibility
        // reasons, it must remain un-prefixed.
        return !$this->belongsToExcludedNamespace($name)
            && (
                $this->reflector->isConstantInternal($name)
                || $this->isExposedConstantFromGlobalNamespace($name)
                || $this->symbolsConfiguration
                    ->getExposedConstants()
                    ->matches($name)
            );
    }

    public function isExposedConstantFromGlobalNamespace(string $constantName): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalConstants() && !strpos($constantName, '\\');
    }

    public function isExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->isRegisteredNamespace($name);
    }

    private function _isExposedFunctionFromGlobalNamespace(string $functionName): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalFunctions() && !strpos($functionName, '\\');
    }

    public function _isExposedClassFromGlobalNamespace(string $className): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalClasses() && !strpos($className, '\\');
    }
}
