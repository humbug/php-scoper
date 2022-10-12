<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\PhpScoperAssertions;

final class NamespaceRegistryAssertions
{
    /**
     * @param list<string> $expectedNames
     * @param list<string> $expectedRegexes
     */
    public static function assertStateIs(
        NamespaceRegistry $namespaceRegistry,
        array $expectedNames,
        array $expectedRegexes
    ): void
    {
        PhpScoperAssertions::assertListEqualsCanonicalizing(
            $expectedNames,
            $namespaceRegistry->getNames(),
        );
        PhpScoperAssertions::assertListEqualsCanonicalizing(
            $expectedRegexes,
            $namespaceRegistry->getRegexes(),
        );
    }

    private function __construct()
    {
    }
}
