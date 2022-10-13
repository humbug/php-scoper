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

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;

final class EnrichedReflectorFactory
{
    public function __construct(private readonly Reflector $reflector)
    {
    }

    public function create(SymbolsConfiguration $symbolsConfiguration): EnrichedReflector
    {
        $configuredReflector = $this->reflector->withAdditionalSymbols(
            $symbolsConfiguration->getExcludedClasses(),
            $symbolsConfiguration->getExcludedFunctions(),
            $symbolsConfiguration->getExcludedConstants(),
        );

        return new EnrichedReflector(
            $configuredReflector,
            $symbolsConfiguration,
        );
    }
}
