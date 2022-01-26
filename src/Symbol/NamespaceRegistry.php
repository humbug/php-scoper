<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use function array_map;
use function array_pop;
use function array_unique;
use function explode;
use function implode;
use function ltrim;
use function Safe\preg_match;
use function Safe\substr;
use function strpos;
use function strtolower;
use const SORT_STRING;

final class NamespaceRegistry
{
    /**
     * @var list<string>
     */
    private array $namespaceNames;

    /**
     * @var list<string>
     */
    private array $namespaceRegexes;

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
                array_map('strtolower', $namespaceNames),
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
        $this->namespaceNames = $namespaceNames;
        $this->namespaceRegexes = $namespaceRegexes;
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
        $originalNamespaceName = ltrim($namespaceName, '\\');
        $normalizedNamespaceName = strtolower($originalNamespaceName);

        foreach ($this->namespaceNames as $excludedNamespaceName) {
            if ('' === $excludedNamespaceName) {
                return true;
            }

            if (0 !== strpos($normalizedNamespaceName, $excludedNamespaceName)) {
                continue;
            }

            $nameParts = explode('\\', $normalizedNamespaceName);

            foreach (explode('\\', $excludedNamespaceName) as $index => $excludedNamespacePart) {
                if ($nameParts[$index] !== $excludedNamespacePart) {
                    return false;
                }
            }

            return true;
        }

        foreach ($this->namespaceRegexes as $excludedNamespace) {
            if (preg_match($excludedNamespace, $originalNamespaceName)) {
                return true;
            }
        }

        return false;
    }

    private static function extractNameNamespace(string $name): string
    {
        if (0 === strpos($name, '\\')) {
            $name = substr($name, 1);
        }

        $nameParts = explode('\\', $name);

        array_pop($nameParts);

        return [] === $nameParts ? '' : implode('\\', $nameParts);
    }
}
