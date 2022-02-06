<?php

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
        $configuredReflector = $this->reflector->withSymbols(
            $symbolsConfiguration->getExcludedClassNames(),
            $symbolsConfiguration->getExcludedFunctionNames(),
            $symbolsConfiguration->getExcludedConstantNames(),
        );

        return new EnrichedReflector(
            $configuredReflector,
            $symbolsConfiguration,
        );
    }
}
