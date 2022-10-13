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
    public function __construct(
        Parser $parser,
        EnrichedReflectorFactory $enrichedReflectorFactory,
        Printer $printer,
        private readonly Scoper $scoper
    ) {
        parent::__construct(
            $parser,
            $enrichedReflectorFactory,
            $printer,
            new Lexer(),
        );
    }

    public function createScoper(Configuration $configuration, SymbolsRegistry $symbolsRegistry): Scoper
    {
        return $this->scoper;
    }
}
