<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use function array_pop;
use function array_unique;
use function explode;
use function implode;
use function ltrim;
use function Safe\preg_match;
use function Safe\substr;
use function strpos;
use function strtolower;

final class NamespaceRegistry
{
    /**
     * @var list<string>
     */
    private array $namespaceRegexes;

    /**
     * @var list<string>
     */
    private array $namespaceNames;

    /**
     * @param string[] $namespaceRegexes
     * @param string[] $namespaceNames
     */
    public static function create(
        array $namespaceRegexes = [],
        array $namespaceNames = []
    ): self {
        return new self(
            array_unique($namespaceRegexes),
            array_unique($namespaceNames),
        );
    }

    /**
     * @param list<string> $namespaceRegexes
     * @param list<string> $namespaceNames
     */
    public function __construct(
        array $namespaceRegexes,
        array $namespaceNames
    ) {
        $this->namespaceRegexes = $namespaceRegexes;
        $this->namespaceNames = $namespaceNames;
    }

    public function belongsToRegisteredNamespace(string $name): bool
    {
        return $this->isRegisteredNamespace(
            self::extractNameNamespace($name),
        );
    }

    public function isRegisteredNamespace(string $name): bool
    {
        $name = strtolower(ltrim($name, '\\'));

        foreach ($this->namespaceNames as $excludedNamespaceName) {
            if ('' === $excludedNamespaceName) {
                return true;
            }

            if (0 !== strpos($name, $excludedNamespaceName)) {
                continue;
            }

            $nameParts = explode('\\', $name);

            foreach (explode('\\', $excludedNamespaceName) as $index => $excludedNamespacePart) {
                if ($nameParts[$index] !== $excludedNamespacePart) {
                    return false;
                }
            }

            return true;
        }

        foreach ($this->namespaceRegexes as $excludedNamespace) {
            if (preg_match($excludedNamespace, $name)) {
                return true;
            }
        }

        return false;
    }

    private static function extractNameNamespace(string $name): string
    {
        $name = strtolower($name);

        if (0 === strpos($name, '\\')) {
            $name = substr($name, 1);
        }

        $nameParts = explode('\\', $name);

        array_pop($nameParts);

        return [] === $nameParts ? '' : implode('\\', $nameParts);
    }
}
