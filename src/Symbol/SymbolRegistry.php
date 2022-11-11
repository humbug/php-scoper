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

use InvalidArgumentException;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_pop;
use function array_unique;
use function explode;
use function implode;
use function ltrim;
use function Safe\preg_match;
use function strtolower;
use function trim;
use const SORT_STRING;

final class SymbolRegistry
{
    /**
     * @var array<string, mixed>
     */
    private array $names;

    /**
     * @param string[] $names
     * @param string[] $regexes
     */
    public static function create(
        array $names = [],
        array $regexes = []
    ): self {
        return new self(
            self::normalizeNames($names),
            array_unique($regexes),
            false,
        );
    }

    /**
     * Unlike classes & functions, constants are not case-insensitive (although
     * the namespace part _is_). I.e. \Acme\FOO = \ACME\FOO but Acme\FOO ≠ Acme\Foo.
     *
     * @param string[] $names
     * @param string[] $regexes
     */
    public static function createForConstants(
        array $names = [],
        array $regexes = []
    ): self {
        return new self(
            self::normalizeConstantNames($names),
            array_unique($regexes),
            true,
        );
    }

    /**
     * @param list<string> $names
     * @param list<string> $regexes
     */
    private function __construct(
        array $names,
        private array $regexes,
        private bool $constants
    ) {
        $this->names = array_flip($names);
    }

    public function matches(string $symbol): bool
    {
        $originalSymbol = ltrim($symbol, '\\');
        $symbol = $this->constants
            ? self::lowerCaseConstantName($originalSymbol)
            : strtolower($originalSymbol);

        if (array_key_exists($symbol, $this->names)) {
            return true;
        }

        foreach ($this->regexes as $regex) {
            if (preg_match($regex, $originalSymbol)) {
                return true;
            }
        }

        return false;
    }

    public function merge(self $registry): self
    {
        if ($this->constants !== $registry->constants) {
            throw new InvalidArgumentException('Cannot merge registries of different symbol types');
        }

        $args = [
            [
                ...$this->getNames(),
                ...$registry->getNames(),
            ],
            [
                ...$this->getRegexes(),
                ...$registry->getRegexes(),
            ],
        ];

        return $this->constants
            ? self::createForConstants(...$args)
            : self::create(...$args);
    }

    /**
     * @internal
     *
     * @return list<string>
     */
    public function getNames(): array
    {
        return array_keys($this->names);
    }

    /**
     * @internal
     *
     * @erturn list<string>
     */
    public function getRegexes(): array
    {
        return $this->regexes;
    }

    private static function normalizeNames(array $names): array
    {
        return array_map(
            static fn (string $name) => strtolower(
                self::normalizeName($name),
            ),
            $names,
        );
    }

    private static function normalizeConstantNames(array $names): array
    {
        return array_map(
            static fn (string $name) => self::lowerCaseConstantName(
                self::normalizeName($name),
            ),
            $names,
        );
    }

    private static function normalizeName(string $name): string
    {
        return trim($name, '\\ ');
    }

    /**
     * Transforms the constant FQ name "Acme\Foo\X" to "acme\foo\X" since the
     * namespace remains case-insensitive for constants regardless of whether
     * constants actually are case-insensitive.
     */
    private static function lowerCaseConstantName(string $name): string
    {
        $parts = explode('\\', $name);

        $lastPart = array_pop($parts);

        $parts = array_map('strtolower', $parts);

        $parts[] = $lastPart;

        return implode('\\', $parts);
    }
}
