<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console\Command;

use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\PhpParser\Printer\Printer;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Lexer;
use PhpParser\Parser;

final class DummyScoperFactory extends ScoperFactory
{
    private Scoper $scoper;

    public function __construct(
        Parser $parser,
        EnrichedReflectorFactory $enrichedReflectorFactory,
        Printer $printer,
        Scoper $scoper
    ) {
        parent::__construct(
            $parser,
            $enrichedReflectorFactory,
            $printer,
            new Lexer(),
        );

        $this->scoper = $scoper;
    }

    public function createScoper(Configuration $configuration, SymbolsRegistry $symbolsRegistry): Scoper
    {
        return $this->scoper;
    }
}
