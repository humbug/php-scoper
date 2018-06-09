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
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function in_array;
use function sprintf;
use function substr;
use function trim;

final class Whitelist implements Countable
{
    private $classes;
    private $namespaces;
    private $whitelistGlobalConstants;

    public static function create(bool $whitelistGlobalConstants, string ...$elements): self
    {
        $classes = [];
        $namespaces = [];

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

            if ('\*' === substr($element, -2)) {
                $namespaces[] = substr($element, 0, -2);
            } elseif ('*' === $element) {
                $namespaces[] = '';
            } else {
                $classes[] = $element;
            }
        }

        return new self(
            $whitelistGlobalConstants,
            array_unique($classes),
            array_unique($namespaces)
        );
    }

    /**
     * @param string[] $classes
     * @param string[] $namespaces
     */
    private function __construct(bool $whitelistGlobalConstants, array $classes, array $namespaces)
    {
        $this->whitelistGlobalConstants = $whitelistGlobalConstants;
        $this->classes = $classes;
        $this->namespaces = $namespaces;
    }

    public function whitelistGlobalConstants(): bool
    {
        return $this->whitelistGlobalConstants;
    }

    public function isClassWhitelisted(string $name): bool
    {
        return in_array($name, $this->classes, true);
    }

    /**
     * @return string[]
     */
    public function getClassWhitelistArray(): array
    {
        return $this->classes;
    }

    public function isNamespaceWhitelisted(string $name): bool
    {
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
        $namespaces = array_map(
            function (string $namespace): string {
                return '' === $namespace ? '*' : $namespace.'\*';
            },
            $this->namespaces
        );

        return array_merge($this->classes, $namespaces);
    }
}
