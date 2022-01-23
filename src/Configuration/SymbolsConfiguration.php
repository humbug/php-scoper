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
use Humbug\PhpScoper\Whitelist;
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
    private array $exposedClassNames;

    /**
     * @var list<string>
     */
    private array $exposedClassRegexes;

    /**
     * @var list<string>
     */
    private array $exposedFunctionNames;

    /**
     * @var list<string>
     */
    private array $exposedFunctionRegexes;

    /**
     * @var list<string>
     */
    private array $exposedConstantNames;

    /**
     * @var list<string>
     */
    private array $exposedConstantRegexes;

    public static function fromWhitelist(Whitelist $whitelist): self
    {
        $exposedSymbols = $whitelist->getExposedSymbols();
        $exposedSymbolsPatterns = $whitelist->getExposedSymbolsPatterns();

        return self::create(
            $whitelist->exposeGlobalConstants(),
            $whitelist->exposeGlobalClasses(),
            $whitelist->exposeGlobalFunctions(),
            $whitelist->getExcludedNamespaces(),
            null,
            $exposedSymbols,
            $exposedSymbolsPatterns,
            $exposedSymbols,
            $exposedSymbolsPatterns,
            $whitelist->getExposedConstants(),
            $exposedSymbolsPatterns,
        );
    }

    /**
     * @param string[] $exposedClassNames
     * @param string[] $exposedClassRegexes
     * @param string[] $exposedFunctionNames
     * @param string[] $exposedFunctionRegexes
     * @param string[] $exposedConstantNames
     * @param string[] $exposedConstantRegexes
     */
    public static function create(
        bool $exposeGlobalConstants = false,
        bool $exposeGlobalClasses = false,
        bool $exposeGlobalFunctions = false,
        ?NamespaceRegistry $excludedNamespaces = null,
        // Does not contain the list of excluded symbols which go to the
        // Reflector (which has no notion of namespaces)
        ?NamespaceRegistry $exposedNamespaces = null,
        array $exposedClassNames = [],
        array $exposedClassRegexes = [],
        array $exposedFunctionNames = [],
        array $exposedFunctionRegexes = [],
        array $exposedConstantNames = [],
        array $exposedConstantRegexes = []
    ): self {
        return new self(
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            $excludedNamespaces ?? NamespaceRegistry::create(),
            $exposedNamespaces ?? NamespaceRegistry::create(),
            array_unique($exposedClassNames),
            array_unique($exposedClassRegexes),
            array_unique($exposedFunctionNames),
            array_unique($exposedFunctionRegexes),
            array_unique($exposedConstantNames),
            array_unique($exposedConstantRegexes),
        );
    }

    /**
     * @param list<string> $exposedClassNames
     * @param list<string> $exposedClassRegexes
     * @param list<string> $exposedFunctionNames
     * @param list<string> $exposedFunctionRegexes
     * @param list<string> $exposedConstantNames
     * @param list<string> $exposedConstantRegexes
     */
    private function __construct(
        bool $exposeGlobalConstants,
        bool $exposeGlobalClasses,
        bool $exposeGlobalFunctions,
        NamespaceRegistry $excludedNamespaces,
        NamespaceRegistry $exposedNamespaces,
        array $exposedClassNames,
        array $exposedClassRegexes,
        array $exposedFunctionNames,
        array $exposedFunctionRegexes,
        array $exposedConstantNames,
        array $exposedConstantRegexes
    ) {
        $this->exposeGlobalConstants = $exposeGlobalConstants;
        $this->exposeGlobalClasses = $exposeGlobalClasses;
        $this->exposeGlobalFunctions = $exposeGlobalFunctions;
        $this->excludedNamespaces = $excludedNamespaces;
        $this->exposedNamespaces = $exposedNamespaces;
        $this->exposedClassNames = $exposedClassNames;
        $this->exposedClassRegexes = $exposedClassRegexes;
        $this->exposedFunctionNames = $exposedFunctionNames;
        $this->exposedFunctionRegexes = $exposedFunctionRegexes;
        $this->exposedConstantNames = $exposedConstantNames;
        $this->exposedConstantRegexes = $exposedConstantRegexes;
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
    public function getExposedClassNames(): array
    {
        return $this->exposedClassNames;
    }

    /**
     * @return list<string>
     */
    public function getExposedClassRegexes(): array
    {
        return $this->exposedClassRegexes;
    }

    /**
     * @return list<string>
     */
    public function getExposedFunctionNames(): array
    {
        return $this->exposedFunctionNames;
    }

    /**
     * @return list<string>
     */
    public function getExposedFunctionRegexes(): array
    {
        return $this->exposedFunctionRegexes;
    }

    /**
     * @return list<string>
     */
    public function getExposedConstantNames(): array
    {
        return $this->exposedConstantNames;
    }

    /**
     * @return list<string>
     */
    public function getExposedConstantRegexes(): array
    {
        return $this->exposedConstantRegexes;
    }
}
