<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;

final class EnrichedReflectorFactory
{
    private Reflector $reflector;

    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
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
