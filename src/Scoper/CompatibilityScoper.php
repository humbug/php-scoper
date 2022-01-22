<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use Humbug\PhpScoper\Whitelist;
use function func_get_args;

/**
 * Ensures the symbols registered to the symbols registry (the new API) are
 * added back to the Whitelist (the old API).
 * This Compatibility layer should only be required until the Box API is adapted.
 */
final class CompatibilityScoper implements Scoper
{
    private Scoper $decoratedScoper;
    private Whitelist $whitelist;
    private SymbolsRegistry $symbolsRegistry;

    public function __construct(
        Scoper $decoratedScoper,
        Whitelist $whitelist,
        SymbolsRegistry $symbolsRegistry
    ) {
        $this->decoratedScoper = $decoratedScoper;
        $this->whitelist = $whitelist;
        $this->symbolsRegistry = $symbolsRegistry;
    }

    public function scope(string $filePath, string $contents): string
    {
        $scopedContents = $this->decoratedScoper->scope(...func_get_args());

        $this->whitelist->registerFromRegistry($this->symbolsRegistry);

        return $scopedContents;
    }
}
