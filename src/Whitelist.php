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

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\PhpParser\NodeVisitor\Collection\WhitelistedFunctionCollection;
use InvalidArgumentException;
use PhpParser\Node\Name\FullyQualified;
use function array_filter;
use function array_flip;
use function array_key_exists;
use function array_map;
use function array_pop;
use function array_unique;
use function count;
use function explode;
use function implode;
use function preg_match;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function substr;
use function trim;

final class Whitelist
{
    private $original;
    private $classes;
    private $constants;
    private $namespaces;
    private $patterns;
    private $whitelistGlobalConstants;
    private $whitelistGlobalFunctions;
    private $whitelistedFunctions;

    public static function create(bool $whitelistGlobalConstants, bool $whitelistGlobalFunctions, string ...$elements): self
    {
        $classes = [];
        $constants = [];
        $namespaces = [];
        $patterns = [];
        $original = [];

        foreach ($elements as $element) {
            if (isset($element[0]) && '\\' === $element[0]) {
                $element = substr($element, 1);
            }

            if ('' === trim($element)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid whitelist element "%s": cannot accept an empty string',
                        $element
                    )
                );
            }

            $original[] = $element;

            if ('\*' === substr($element, -2)) {
                $namespaces[] = strtolower(substr($element, 0, -2));
            } elseif ('*' === $element) {
                $namespaces[] = '';
            } elseif (false !== strpos($element, '*')) {
                self::assertValidPattern($element);

                $patterns[] = sprintf(
                    '/^%s$/ui',
                    strtolower(
                        str_replace(
                            '\\',
                            '\\\\',
                            str_replace(
                                '*',
                                '.*',
                                $element
                            )
                        )
                    )
                );
            } else {
                $classes[] = strtolower($element);
                $constants[] = self::lowerConstantName($element);
            }
        }

        return new self(
            $whitelistGlobalConstants,
            $whitelistGlobalFunctions,
            array_unique($original),
            array_flip($classes),
            array_flip($constants),
            array_unique($patterns),
            array_unique($namespaces)
        );
    }

    private static function assertValidPattern(string $element): void
    {
        if (1 !== preg_match('/^(([\p{L}_]+\\\\)+)?[\p{L}_]*\*$/u', $element)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid whitelist pattern "%s".',
                    $element
                )
            );
        }
    }

    /**
     * @param string[] $original
     * @param string[] $patterns
     * @param string[] $namespaces
     */
    private function __construct(
        bool $whitelistGlobalConstants,
        bool $whitelistGlobalFunctions,
        array $original,
        array $classes,
        array $constants,
        array $patterns,
        array $namespaces
    ) {
        $this->whitelistGlobalConstants = $whitelistGlobalConstants;
        $this->whitelistGlobalFunctions = $whitelistGlobalFunctions;
        $this->original = $original;
        $this->classes = $classes;
        $this->constants = $constants;
        $this->namespaces = $namespaces;
        $this->patterns = $patterns;
        $this->whitelistedFunctions = new WhitelistedFunctionCollection();
    }

    public function recordWhitelistedFunction(FullyQualified $original, FullyQualified $alias): void
    {
        $this->whitelistedFunctions->add($original, $alias);
    }

    public function getWhitelistedFunctions(): WhitelistedFunctionCollection
    {
        return $this->whitelistedFunctions;
    }

    public function whitelistGlobalConstants(): bool
    {
        return $this->whitelistGlobalConstants;
    }

    public function whitelistGlobalFunctions(): bool
    {
        return $this->whitelistGlobalFunctions;
    }

    public function isClassWhitelisted(string $name): bool
    {
        if (array_key_exists(strtolower($name), $this->classes)) {
            return true;
        }

        foreach ($this->patterns as $pattern) {
            if (1 === preg_match($pattern, $name)) {
                return true;
            }
        }

        return false;
    }

    public function isConstantWhitelisted(string $name): bool
    {
        if (array_key_exists(self::lowerConstantName($name), $this->constants)) {
            return true;
        }

        foreach ($this->patterns as $pattern) {
            if (1 === preg_match($pattern, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getClassWhitelistArray(): array
    {
        return array_filter(
            $this->original,
            function (string $name): bool {
                return '*' !== $name && '\*' !== substr($name, -2);
            }
        );
    }

    public function isNamespaceWhitelisted(string $name): bool
    {
        $name = strtolower($name);

        foreach ($this->namespaces as $namespace) {
            if ('' === $namespace || 0 === strpos($name, $namespace)) {
                return true;
            }
        }

        return false;
    }

    public function toArray(): array
    {
        return $this->original;
    }

    public function hasWhitelistStatements(): bool
    {
        return count($this->classes) + count($this->whitelistedFunctions) + count($this->patterns) > 0;
    }

    private static function lowerConstantName(string $name): string
    {
        $parts = explode('\\', $name);

        $lastPart = array_pop($parts);

        $parts = array_map('strtolower', $parts);

        $parts[] = $lastPart;

        return implode('\\', $parts);
    }
}
