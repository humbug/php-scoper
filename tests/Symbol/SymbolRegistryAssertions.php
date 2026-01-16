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

use Humbug\PhpScoper\PhpScoperAssertions;

final class SymbolRegistryAssertions
{
    /**
     * @param string[] $expectedNames
     * @param string[] $expectedRegexes
     */
    public static function assertStateIs(
        SymbolRegistry $symbolRegistry,
        array $expectedNames,
        array $expectedRegexes,
    ): void {
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
