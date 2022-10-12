<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\PhpScoperAssertions;

final class SymbolRegistryAssertions
{
    /**
     * @param list<string> $expectedNames
     * @param list<string> $expectedRegexes
     */
    public static function assertStateIs(
        SymbolRegistry $symbolRegistry,
        array $expectedNames,
        array $expectedRegexes
    ): void
    {
        PhpScoperAssertions::assertListEqualsCanonicalizing(
            $expectedNames,
            $symbolRegistry->getNames(),
        );
        PhpScoperAssertions::assertListEqualsCanonicalizing(
            $expectedRegexes,
            $symbolRegistry->getRegexes(),
        );
    }

    private function __construct()
    {
    }
}
