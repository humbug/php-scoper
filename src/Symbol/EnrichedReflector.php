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

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use function ltrim;
use function str_contains;

/**
 * Combines the API or the "traditional" reflector which is about to tell
 * if a symbol is internal or not with the more PHP-Scoper specific exposed
 * API.
 *
 * To recap, the configurations to take into account:
 * - excluded & exposed namespaces (exclusion takes priority)
 * - internal symbols
 * - exposed symbols
 * - whether symbols from the global namespace should be exposed or not
 */
final readonly class EnrichedReflector
{
    public function __construct(
        private Reflector $reflector,
        private SymbolsConfiguration $symbolsConfiguration,
    ) {
    }

    public function belongsToExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->belongsToRegisteredNamespace($name);
    }

    private function belongsToExposedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExposedNamespaces()
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

    public function isExposedFunction(string $resolvedName): bool
    {
        return !$this->isFunctionExcluded($resolvedName)
            && (
                $this->isExposedFunctionFromGlobalNamespaceWithoutExclusionCheck($resolvedName)
                || $this->symbolsConfiguration
                    ->getExposedFunctions()
                    ->matches($resolvedName)
                || $this->belongsToExposedNamespace($resolvedName)
            );
    }

    public function isExposedFunctionFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->isFunctionExcluded($resolvedName)
            && $this->isExposedFunctionFromGlobalNamespaceWithoutExclusionCheck($resolvedName);
    }

    public function isExposedClass(string $resolvedName): bool
    {
        return !$this->isClassExcluded($resolvedName)
            && (
                $this->isExposedClassFromGlobalNamespaceWithoutExclusionCheck($resolvedName)
                || $this->symbolsConfiguration
                    ->getExposedClasses()
                    ->matches($resolvedName)
                || $this->belongsToExposedNamespace($resolvedName)
            );
    }

    public function isExposedClassFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->isClassExcluded($resolvedName)
            && $this->isExposedClassFromGlobalNamespaceWithoutExclusionCheck($resolvedName);
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
                || $this->belongsToExposedNamespace($name)
            );
    }

    public function isExposedConstantFromGlobalNamespace(string $constantName): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalConstants()
            && $this->belongsToGlobalNamespace($constantName);
    }

    public function isExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->isRegisteredNamespace($name);
    }

    private function isExposedFunctionFromGlobalNamespaceWithoutExclusionCheck(string $functionName): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalFunctions()
            && $this->belongsToGlobalNamespace($functionName);
    }

    private function isExposedClassFromGlobalNamespaceWithoutExclusionCheck(string $className): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalClasses()
            && $this->belongsToGlobalNamespace($className);
    }

    public function belongsToGlobalNamespace(string $symbolName): bool
    {
        return !str_contains(
            ltrim($symbolName, '\\'),
            '\\',
        );
    }
}
