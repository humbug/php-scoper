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

use Humbug\PhpScoper\NotInstantiable;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\SymbolRegistry;

final readonly class SymbolsConfiguration
{
    use NotInstantiable;

    // To keep in sync with the default configuration set in src/scoper.inc.php.tpl
    public static function create(
        bool $exposeGlobalConstants = true,
        bool $exposeGlobalClasses = true,
        bool $exposeGlobalFunctions = true,
        ?NamespaceRegistry $excludedNamespaces = null,
        // Does not contain the list of excluded symbols which go to the
        // Reflector (which has no notion of namespaces)
        ?NamespaceRegistry $exposedNamespaces = null,
        ?SymbolRegistry $exposedClasses = null,
        ?SymbolRegistry $exposedFunctions = null,
        ?SymbolRegistry $exposedConstants = null,
        ?SymbolRegistry $excludedClasses = null,
        ?SymbolRegistry $excludedFunctions = null,
        ?SymbolRegistry $excludedConstants = null,
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
            $excludedClasses ?? SymbolRegistry::create(),
            $excludedFunctions ?? SymbolRegistry::create(),
            $excludedConstants ?? SymbolRegistry::createForConstants(),
        );
    }

    private function __construct(
        private bool $exposeGlobalConstants,
        private bool $exposeGlobalClasses,
        private bool $exposeGlobalFunctions,
        private NamespaceRegistry $excludedNamespaces,
        private NamespaceRegistry $exposedNamespaces,
        private SymbolRegistry $exposedClasses,
        private SymbolRegistry $exposedFunctions,
        private SymbolRegistry $exposedConstants,
        private SymbolRegistry $excludedClasses,
        private SymbolRegistry $excludedFunctions,
        private SymbolRegistry $excludedConstants,
    ) {
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

    public function getExcludedClasses(): SymbolRegistry
    {
        return $this->excludedClasses;
    }

    public function getExcludedFunctions(): SymbolRegistry
    {
        return $this->excludedFunctions;
    }

    public function getExcludedConstants(): SymbolRegistry
    {
        return $this->excludedConstants;
    }
}
