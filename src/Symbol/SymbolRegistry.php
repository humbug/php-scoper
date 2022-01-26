<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_pop;
use function array_unique;
use function explode;
use function implode;
use function ltrim;
use function Safe\array_flip;
use function Safe\preg_match;
use function strtolower;

final class SymbolRegistry
{
    /**
     * @var array<string, mixed>
     */
    private array $names;

    /**
     * @var list<string>
     */
    private array $regexes;

    private bool $constants;

    /**
     * @param string[] $names
     * @param string[] $regexes
     */
    public static function create(
        array $names = [],
        array $regexes = []
    ): self {
        return new self(
            array_unique(
                array_map(
                    static fn (string $name) => strtolower(ltrim($name, '\\')),
                    $names,
                ),
            ),
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
            array_unique(
                array_map(
                    static fn (string $name) => self::lowerCaseConstantName(
                        ltrim($name, '\\'),
                    ),
                    $names,
                ),
            ),
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
        array $regexes,
        bool $constants
    ) {
        $this->names = array_flip($names);
        $this->regexes = $regexes;
        $this->constants = $constants;
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
