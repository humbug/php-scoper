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

use Countable;
use InvalidArgumentException;
use function array_filter;
use function array_map;
use function array_pop;
use function array_unique;
use function count;
use function explode;
use function implode;
use function in_array;
use function sprintf;
use function strtolower;
use function substr;
use function trim;

final class Whitelist implements Countable
{
    private $original;
    private $classes;
    private $constants;
    private $namespaces;
    private $whitelistGlobalConstants;

    public static function create(bool $whitelistGlobalConstants, string ...$elements): self
    {
        $classes = [];
        $constants = [];
        $namespaces = [];
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
                $namespace = strtolower(substr($element, 0, -2));

                $namespaces[] = $namespace;
            } elseif ('*' === $element) {
                $namespaces[] = '';
            } else {
                $classes[] = strtolower($element);
                $constants[] = self::lowerConstantName($element);
            }
        }

        return new self(
            $whitelistGlobalConstants,
            array_unique($original),
            array_unique($classes),
            array_unique($constants),
            array_unique($namespaces)
        );
    }

    /**
     * @param string[] $original
     * @param string[] $classes
     * @param string[] $constants
     * @param string[] $namespaces
     */
    private function __construct(
        bool $whitelistGlobalConstants,
        array $original,
        array $classes,
        array $constants,
        array $namespaces
    ) {
        $this->whitelistGlobalConstants = $whitelistGlobalConstants;
        $this->original = $original;
        $this->classes = $classes;
        $this->constants = $constants;
        $this->namespaces = $namespaces;
    }

    public function whitelistGlobalConstants(): bool
    {
        return $this->whitelistGlobalConstants;
    }

    public function isClassWhitelisted(string $name): bool
    {
        return in_array(strtolower($name), $this->classes, true);
    }

    public function isConstantWhitelisted(string $name): bool
    {
        return in_array(self::lowerConstantName($name), $this->constants, true);
    }

    /**
     * @return string[]
     */
    public function getClassWhitelistArray(): array
    {
        return array_filter(
            $this->original,
            function (string $name): bool {
                return '' !== $name && '\*' !== substr($name, -2);
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

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->classes) + count($this->namespaces);
    }

    public function toArray(): array
    {
        return $this->original;
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
