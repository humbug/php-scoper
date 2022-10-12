<?php

declare(strict_types=1);

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

final class NamespaceRegistry
{
    /**
     * @var list<string>
     */
    private array $names;

    /**
     * @var list<string>
     */
    private array $regexes;

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
            array_unique(
                array_map(
                    static fn (string $namespaceName) => strtolower(trim($namespaceName, '\\')),
                    $namespaceNames,
                ),
                SORT_STRING,
            ),
            array_unique($namespaceRegexes, SORT_STRING),
        );
    }

    /**
     * @param list<string> $namespaceNames
     * @param list<string> $namespaceRegexes
     */
    private function __construct(
        array $namespaceNames,
        array $namespaceRegexes
    ) {
        $this->names = $namespaceNames;
        $this->regexes = $namespaceRegexes;

        $this->containsGlobalNamespace = count(
                array_filter(
                    $namespaceNames,
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
