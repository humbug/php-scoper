<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use function array_key_exists;
use function array_map;
use function array_pop;
use function array_unique;
use function explode;
use function implode;
use function ltrim;
use function Safe\array_flip;
use function Safe\preg_match;
use function Safe\substr;
use function strpos;
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
                array_map('strtolower', $names),
            ),
            array_unique($regexes),
        );
    }

    /**
     * @param list<string> $names
     * @param list<string> $regexes
     */
    private function __construct(
        array $names,
        array $regexes
    ) {
        $this->names = array_flip($names);
        $this->regexes = $regexes;
    }

    public function matches(string $symbol): bool
    {
        $originalSymbol = ltrim($symbol, '\\');
        $symbol = strtolower($originalSymbol);

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
}
