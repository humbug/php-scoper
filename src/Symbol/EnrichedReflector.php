<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use function ltrim;
use function str_contains;

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
        // TODO: review as not used at the moment
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
                || $this->belongsToExposedNamespace($resolvedName)
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
                || $this->belongsToExposedNamespace($resolvedName)
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
                || $this->belongsToExposedNamespace($name)
            );
    }

    public function isExposedConstantFromGlobalNamespace(string $constantName): bool
    {
        // TODO: leverage belongsToGlobalNamespace
        return $this->symbolsConfiguration->shouldExposeGlobalConstants() && !mb_strpos($constantName, '\\');
    }

    public function isExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->isRegisteredNamespace($name);
    }

    public function _isExposedClassFromGlobalNamespace(string $className): bool
    {
        // TODO: leverage belongsToGlobalNamespace
        return $this->symbolsConfiguration->shouldExposeGlobalClasses() && !mb_strpos($className, '\\');
    }

    public function belongsToGlobalNamespace(string $symbolName): bool
    {
        return !str_contains(
            ltrim($symbolName, '\\'),
            '\\',
        );
    }

    private function belongsToExposedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExposedNamespaces()
            ->belongsToRegisteredNamespace($name);
    }

    private function _isExposedFunctionFromGlobalNamespace(string $functionName): bool
    {
        // TODO: leverage belongsToGlobalNamespace
        return $this->symbolsConfiguration->shouldExposeGlobalFunctions() && !mb_strpos($functionName, '\\');
    }
}
