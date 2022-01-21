<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;

/**
 * Combines the API or the "traditional" reflector which is about to tell
 * if a symbol is internal or not with the more PHP-Scoper specific exposed
 * API.
 */
final class EnrichedReflector
{
    private Reflector $reflector;
    private Whitelist $whitelist;

    public function __construct(Reflector $reflector, Whitelist $whitelist)
    {
        $this->reflector = $reflector;
        $this->whitelist = $whitelist;
    }

    public function belongsToExcludedNamespace(string $name): bool
    {
        return $this->whitelist->belongsToExcludedNamespace($name);
    }

    public function isFunctionInternal(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name);
    }

    public function isFunctionExcluded(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name)
            || $this->whitelist->belongsToExcludedNamespace($name);
    }

    public function isClassInternal(string $name): bool
    {
        return $this->reflector->isClassInternal($name);
    }

    public function isClassExcluded(string $name): bool
    {
        return $this->reflector->isClassInternal($name)
            || $this->whitelist->belongsToExcludedNamespace($name);
    }

    public function isConstantInternal(string $name): bool
    {
        return $this->reflector->isConstantInternal($name);
    }

    public function isConstantExcluded(string $name): bool
    {
        // TODO: double check not sure that internal should mean excluded for constants
        return $this->reflector->isConstantInternal($name)
            || $this->whitelist->belongsToExcludedNamespace($name);
    }

    public function isExposedFunction(string $resolvedName): bool
    {
        return !$this->whitelist->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isFunctionInternal($resolvedName)
            && (
                $this->whitelist->isExposedFunctionFromGlobalNamespace($resolvedName)
                || $this->whitelist->isSymbolExposed($resolvedName)
            );
    }

    public function isExposedFunctionFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->whitelist->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isFunctionInternal($resolvedName)
            && $this->whitelist->isExposedFunctionFromGlobalNamespace($resolvedName);
    }

    public function isExposedClass(string $resolvedName): bool
    {
        return !$this->whitelist->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isClassInternal($resolvedName)
            && (
                $this->whitelist->isExposedClassFromGlobalNamespace($resolvedName)
                || $this->whitelist->isSymbolExposed($resolvedName)
            );
    }

    public function isExposedClassFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->whitelist->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isClassInternal($resolvedName)
            && $this->whitelist->isExposedClassFromGlobalNamespace($resolvedName);
    }

    public function isExposedConstant(string $name): bool
    {
        // Special case: internal constants must be treated as exposed symbols.
        //
        // Example: when declaring a new internal constant for compatibility
        // reasons, it must remain un-prefixed.
        return !$this->whitelist->belongsToExcludedNamespace($name)
            && (
                $this->reflector->isConstantInternal($name)
                || $this->whitelist->isExposedConstantFromGlobalNamespace($name)
                || $this->whitelist->isSymbolExposed($name, true)
            );
    }

    public function isExposedConstantFromGlobalNamespace(string $constantName): bool
    {
        return $this->whitelist->isExposedConstantFromGlobalNamespace($constantName);
    }

    public function isExcludedNamespace(string $name): bool
    {
        return $this->whitelist->isExcludedNamespace($name);
    }
}
