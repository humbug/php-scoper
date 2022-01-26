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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\SymbolRegistry;

final class SymbolsConfiguration
{
    private bool $exposeGlobalConstants;
    private bool $exposeGlobalClasses;
    private bool $exposeGlobalFunctions;

    private NamespaceRegistry $excludedNamespaces;
    private NamespaceRegistry $exposedNamespaces;

    private SymbolRegistry $exposedClasses;
    private SymbolRegistry $exposedFunctions;
    private SymbolRegistry $exposedConstants;

    /**
     * @var string[]
     */
    private array $excludedClassNames;

    /**
     * @var string[]
     */
    private array $excludedFunctionNames;

    /**
     * @var string[]
     */
    private array $excludedConstantNames;

    /**
     * @param string[] $excludedClassNames
     * @param string[] $excludedFunctionNames
     * @param string[] $excludedConstantNames
     */
    public static function create(
        bool $exposeGlobalConstants = false,
        bool $exposeGlobalClasses = false,
        bool $exposeGlobalFunctions = false,
        ?NamespaceRegistry $excludedNamespaces = null,
        // Does not contain the list of excluded symbols which go to the
        // Reflector (which has no notion of namespaces)
        ?NamespaceRegistry $exposedNamespaces = null,
        SymbolRegistry $exposedClasses = null,
        SymbolRegistry $exposedFunctions = null,
        SymbolRegistry $exposedConstants = null,
        array $excludedClassNames = [],
        array $excludedFunctionNames = [],
        array $excludedConstantNames = []
    ): self {
        return new self(
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            $excludedNamespaces ?? NamespaceRegistry::create(),
            $exposedNamespaces ?? NamespaceRegistry::create(),
            $exposedClasses ?? SymbolRegistry::create(),
            $exposedFunctions ?? SymbolRegistry::create(),
            $exposedConstants ?? SymbolRegistry::createForConstants(),
            $excludedClassNames,
            $excludedFunctionNames,
            $excludedConstantNames,
        );
    }

    /**
     * @param string[] $excludedClassNames
     * @param string[] $excludedFunctionNames
     * @param string[] $excludedConstantNames
     */
    private function __construct(
        bool $exposeGlobalConstants,
        bool $exposeGlobalClasses,
        bool $exposeGlobalFunctions,
        NamespaceRegistry $excludedNamespaces,
        NamespaceRegistry $exposedNamespaces,
        SymbolRegistry $exposedClasses,
        SymbolRegistry $exposedFunctions,
        SymbolRegistry $exposedConstants,
        array $excludedClassNames,
        array $excludedFunctionNames,
        array $excludedConstantNames
    ) {
        $this->exposeGlobalConstants = $exposeGlobalConstants;
        $this->exposeGlobalClasses = $exposeGlobalClasses;
        $this->exposeGlobalFunctions = $exposeGlobalFunctions;
        $this->excludedNamespaces = $excludedNamespaces;
        $this->exposedNamespaces = $exposedNamespaces;
        $this->exposedClasses = $exposedClasses;
        $this->exposedFunctions = $exposedFunctions;
        $this->exposedConstants = $exposedConstants;
        $this->excludedClassNames = $excludedClassNames;
        $this->excludedFunctionNames = $excludedFunctionNames;
        $this->excludedConstantNames = $excludedConstantNames;
    }

    public function shouldExposeGlobalConstants(): bool
    {
        return $this->exposeGlobalConstants;
    }

    public function shouldExposeGlobalClasses(): bool
    {
        return $this->exposeGlobalClasses;
    }

    public function shouldExposeGlobalFunctions(): bool
    {
        return $this->exposeGlobalFunctions;
    }

    public function getExcludedNamespaces(): NamespaceRegistry
    {
        return $this->excludedNamespaces;
    }

    public function getExposedNamespaces(): NamespaceRegistry
    {
        return $this->exposedNamespaces;
    }

    public function getExposedClasses(): SymbolRegistry
    {
        return $this->exposedClasses;
    }

    public function getExposedFunctions(): SymbolRegistry
    {
        return $this->exposedFunctions;
    }

    public function getExposedConstants(): SymbolRegistry
    {
        return $this->exposedConstants;
    }

    /**
     * @return string[]
     */
    public function getExcludedClassNames(): array
    {
        return $this->excludedClassNames;
    }

    /**
     * @return string[]
     */
    public function getExcludedFunctionNames(): array
    {
        return $this->excludedFunctionNames;
    }

    /**
     * @return string[]
     */
    public function getExcludedConstantNames(): array
    {
        return $this->excludedConstantNames;
    }
}
