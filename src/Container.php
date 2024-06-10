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

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\PhpParser\Printer\Printer;
use Humbug\PhpScoper\PhpParser\Printer\StandardPrinter;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;
use PhpParser\Parser\Php7;
use PhpParser\Parser\Php8;
use PhpParser\PhpVersion;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Filesystem\Filesystem;

final class Container
{
    private Filesystem $filesystem;
    private ConfigurationFactory $configFactory;
    private Parser $parser;
    private Reflector $reflector;
    private ScoperFactory $scoperFactory;
    private EnrichedReflectorFactory $enrichedReflectorFactory;
    private Printer $printer;

    public function getFileSystem(): Filesystem
    {
        if (!isset($this->filesystem)) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    public function getConfigurationFactory(): ConfigurationFactory
    {
        if (!isset($this->configFactory)) {
            $this->configFactory = new ConfigurationFactory(
                $this->getFileSystem(),
                new SymbolsConfigurationFactory(
                    new RegexChecker(),
                ),
            );
        }

        return $this->configFactory;
    }

    /**
     * @param string|null $phpVersion PHP version to parse as, e.g. '7.2'. Fallbacks to host version.
     */
    public function getScoperFactory(?string $phpVersion = null): ScoperFactory
    {
        if (!isset($this->scoperFactory)) {
            $this->scoperFactory = new ScoperFactory(
                $this->getParser($phpVersion),
                $this->getEnrichedReflectorFactory(),
                $this->getPrinter(),
            );
        }

        return $this->scoperFactory;
    }

    /**
     * @param string|null $phpVersion PHP version to parse as, e.g. '7.2'. Fallbacks to host version.
     */
    public function getParser(?string $phpVersion = null): Parser
    {
        if (!isset($this->parser)) {
            // TODO: add assert
            $this->parser = $this->createParser($phpVersion);
        }

        return $this->parser;
    }

    /**
     * @param string|null $phpVersion PHP version to parse as, e.g. '7.2'. Fallbacks to host version.
     */
    private function createParser(?string $phpVersion): Parser
    {
        $version = null === $phpVersion ?
            PhpVersion::getHostVersion() :
            PhpVersion::fromString($phpVersion);
        $lexer = $version->isHostVersion() ? new Lexer() : new Emulative($version);

        return $version->id >= 80_000
            ? new Php8($lexer, $version)
            : new Php7($lexer, $version);
    }

    public function getReflector(): Reflector
    {
        if (!isset($this->reflector)) {
            $this->reflector = Reflector::createWithPhpStormStubs();
        }

        return $this->reflector;
    }

    public function getEnrichedReflectorFactory(): EnrichedReflectorFactory
    {
        if (!isset($this->enrichedReflectorFactory)) {
            $this->enrichedReflectorFactory = new EnrichedReflectorFactory(
                $this->getReflector(),
            );
        }

        return $this->enrichedReflectorFactory;
    }

    public function getPrinter(): Printer
    {
        if (!isset($this->printer)) {
            $this->printer = new StandardPrinter(
                new Standard(),
            );
        }

        return $this->printer;
    }
}
