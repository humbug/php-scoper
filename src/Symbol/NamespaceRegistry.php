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

use function array_filter;
use function array_map;
use function array_pop;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function implode;
use function ltrim;
use function Safe\preg_match;
use function str_contains;
use function strtolower;
use function trim;
use const SORT_STRING;

final readonly class NamespaceRegistry
{
    private bool $containsGlobalNamespace;

    /**
     * @param string[] $namespaceNames
     * @param string[] $namespaceRegexes
     */
    public static function create(
        array $namespaceNames = [],
        array $namespaceRegexes = []
    ): self {
        return new self(
            array_values(
                array_unique(
                    array_map(
                        static fn (string $namespaceName) => strtolower(trim($namespaceName, '\\')),
                        $namespaceNames,
                    ),
                    SORT_STRING,
                ),
            ),
            array_values(
                array_unique($namespaceRegexes, SORT_STRING),
            ),
        );
    }

    /**
     * @param list<string> $names
     * @param list<string> $regexes
     */
    private function __construct(
        private array $names,
        private array $regexes
    ) {
        $this->containsGlobalNamespace = count(
            array_filter(
                $names,
                static fn (string $name) => '' === $name,
            ),
        ) !== 0;
    }

    public function belongsToRegisteredNamespace(string $symbolName): bool
    {
        return $this->isRegisteredNamespace(
            self::extractNameNamespace($symbolName),
        );
    }

    /**
     * Checks if the given namespace matches one of the registered namespace
     * names, is a sub-namespace of a registered namespace name or matches any
     * regex provided.
     */
    public function isRegisteredNamespace(string $namespaceName): bool
    {
        if ($this->containsGlobalNamespace) {
            return true;
        }

        $originalNamespaceName = ltrim($namespaceName, '\\');
        $normalizedNamespaceName = strtolower($originalNamespaceName);

        foreach ($this->names as $excludedNamespaceName) {
            if ('' === $excludedNamespaceName
                || str_contains($normalizedNamespaceName, $excludedNamespaceName)
            ) {
                return true;
            }
        }

        foreach ($this->regexes as $excludedNamespace) {
            if (preg_match($excludedNamespace, $originalNamespaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @internal
     *
     * @return list<string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @internal
     *
     * @return list<string>
     */
    public function getRegexes(): array
    {
        return $this->regexes;
    }

    private static function extractNameNamespace(string $name): string
    {
        $nameParts = explode('\\', $name);

        array_pop($nameParts);

        return [] === $nameParts ? '' : implode('\\', $nameParts);
    }
}
