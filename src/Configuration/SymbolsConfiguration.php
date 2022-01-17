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
use function array_unique;

final class SymbolsConfiguration
{
    private bool $exposeGlobalConstants;
    private bool $exposeGlobalClasses;
    private bool $exposeGlobalFunctions;

    private NamespaceRegistry $excludedNamespaces;
    private NamespaceRegistry $exposedNamespaces;

    /**
     * @var list<string>
     */
    private array $exposedClasses;

    /**
     * @var list<string>
     */
    private array $exposedFunctions;

    /**
     * @var list<string>
     */
    private array $exposedConstants;

    /**
     * @param string[] $excludedNamespaceRegexes
     * @param string[] $excludedNamespaceNames
     * @param string[] $exposedNamespaceRegexes
     * @param string[] $exposedNamespaceNames
     * @param string[] $exposedClasses
     * @param string[] $exposedFunctions
     * @param string[] $exposedConstants
     */
    public static function create(
        bool $exposeGlobalConstants = true,
        bool $exposeGlobalClasses = true,
        bool $exposeGlobalFunctions = true,
        ?NamespaceRegistry $excludedNamespaces = null,
        // Does not contain the list of excluded symbols which go to the
        // Reflector (which has no notion of namespaces)
        ?NamespaceRegistry $exposedNamespaces = null,
        array $exposedClasses = [],
        array $exposedFunctions = [],
        array $exposedConstants = []
    ): self {
        return new self(
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            $excludedNamespaces ?? NamespaceRegistry::create(),
            $exposedNamespaces ?? NamespaceRegistry::create(),
            array_unique($exposedClasses),
            array_unique($exposedFunctions),
            array_unique($exposedConstants),
        );
    }

    /**
     * @param list<string>       $exposedClasses
     * @param list<string>       $exposedFunctions
     * @param list<string>       $exposedConstants
     */
    public function __construct(
        bool $exposeGlobalConstants,
        bool $exposeGlobalClasses,
        bool $exposeGlobalFunctions,
        NamespaceRegistry $excludedNamespaces,
        NamespaceRegistry $exposedNamespaces,
        array $exposedClasses,
        array $exposedFunctions,
        array $exposedConstants
    ) {
        $this->exposeGlobalConstants = $exposeGlobalConstants;
        $this->exposeGlobalClasses = $exposeGlobalClasses;
        $this->exposeGlobalFunctions = $exposeGlobalFunctions;
        $this->excludedNamespaces = $excludedNamespaces;
        $this->exposedNamespaces = $exposedNamespaces;
        $this->exposedClasses = $exposedClasses;
        $this->exposedFunctions = $exposedFunctions;
        $this->exposedConstants = $exposedConstants;
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

    /**
     * @return list<string>
     */
    public function getExposedClasses(): array
    {
        return $this->exposedClasses;
    }

    /**
     * @return list<string>
     */
    public function getExposedFunctions(): array
    {
        return $this->exposedFunctions;
    }

    /**
     * @return list<string>
     */
    public function getExposedConstants(): array
    {
        return $this->exposedConstants;
    }
}
