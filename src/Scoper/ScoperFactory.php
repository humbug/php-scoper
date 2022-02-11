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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Scoper\Composer\AutoloadPrefixer;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;

/**
 * @final
 */
class ScoperFactory
{
    private Parser $parser;
    private Lexer $lexer;
    private PrettyPrinterAbstract $printer;
    private EnrichedReflectorFactory $enrichedReflectorFactory;

    public function __construct(
        Parser $parser,
        Lexer $lexer,
        PrettyPrinterAbstract $printer,
        EnrichedReflectorFactory $enrichedReflectorFactory
    ) {
        $this->parser = $parser;
        $this->lexer = $lexer;
        $this->printer = $printer;
        $this->enrichedReflectorFactory = $enrichedReflectorFactory;
    }

    public function createScoper(
        Configuration $configuration,
        SymbolsRegistry $symbolsRegistry
    ): Scoper
    {
        $prefix = $configuration->getPrefix();
        $symbolsConfiguration = $configuration->getSymbolsConfiguration();
        $enrichedReflector = $this->enrichedReflectorFactory->create($symbolsConfiguration);

        $autoloadPrefixer = new AutoloadPrefixer(
            $prefix,
            $enrichedReflector,
        );

        return new PatchScoper(
            new PhpScoper(
                $this->parser,
                $this->lexer,
                $this->printer,
                new JsonFileScoper(
                    new InstalledPackagesScoper(
                        new SymfonyScoper(
                            new NullScoper(),
                            $prefix,
                            $enrichedReflector,
                            $symbolsRegistry,
                        ),
                        $autoloadPrefixer
                    ),
                    $autoloadPrefixer
                ),
                new TraverserFactory($enrichedReflector),
                $prefix,
                $symbolsRegistry,
            ),
            $prefix,
            $configuration->getPatcher(),
        );
    }
}
