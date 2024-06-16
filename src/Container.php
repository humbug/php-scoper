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
use Humbug\PhpScoper\PhpParser\Parser\ParserFactory;
use Humbug\PhpScoper\PhpParser\Parser\StandardParserFactory;
use Humbug\PhpScoper\PhpParser\Printer\Printer;
use Humbug\PhpScoper\PhpParser\Printer\PrinterFactory;
use Humbug\PhpScoper\PhpParser\Printer\StandardPrinter;
use Humbug\PhpScoper\PhpParser\Printer\StandardPrinterFactory;
use Humbug\PhpScoper\Scoper\Factory\ScoperFactory;
use Humbug\PhpScoper\Scoper\Factory\StandardScoperFactory;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use PhpParser\Parser;
use PhpParser\PhpVersion;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

final class Container
{
    private Filesystem $filesystem;
    private ConfigurationFactory $configFactory;
    private ParserFactory $parserFactory;
    private Parser $parser;
    private ?PhpVersion $parserPhpVersion = null;
    private ?PhpVersion $printerPhpVersion = null;
    private Reflector $reflector;
    private ScoperFactory $scoperFactory;
    private EnrichedReflectorFactory $enrichedReflectorFactory;
    private PrinterFactory $printerFactory;
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

    public function getScoperFactory(): ScoperFactory
    {
        if (!isset($this->scoperFactory)) {
            $this->scoperFactory = new StandardScoperFactory(
                $this->getEnrichedReflectorFactory(),
                $this->getParserFactory(),
                $this->getPrinterFactory(),
            );
        }

        return $this->scoperFactory;
    }

    /**
     * @deprecated Use ::getParserFactory() instead.
     */
    public function getParser(?PhpVersion $phpVersion = null): Parser
    {
        if (!isset($this->parser)) {
            $this->parserPhpVersion = $phpVersion;
            $this->parser = $this->getParserFactory()->createParser($phpVersion);
        }

        self::checkSamePhpVersion($this->parserPhpVersion, $phpVersion);

        return $this->parser;
    }

    public function getParserFactory(): ParserFactory
    {
        if (!isset($this->parserFactory)) {
            $this->parserFactory = new StandardParserFactory();
        }

        return $this->parserFactory;
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

    /**
     * @deprecated use ::getPrinterFactory() instead.
     */
    public function getPrinter(?PhpVersion $phpVersion = null): Printer
    {
        if (!isset($this->printer)) {
            $this->printerPhpVersion = $phpVersion;
            $this->printer = new StandardPrinter(
                new Standard([
                    'phpVersion' => $phpVersion,
                ]),
            );
        }

        self::checkSamePhpVersion($this->printerPhpVersion, $phpVersion);

        return $this->printer;
    }

    public function getPrinterFactory(): PrinterFactory
    {
        if (!isset($this->printerFactory)) {
            $this->printerFactory = new StandardPrinterFactory();
        }

        return $this->printerFactory;
    }

    private static function checkSamePhpVersion(
        ?PhpVersion $versionUsed,
        ?PhpVersion $versionRequest,
    ): void {
        $parserMessage = 'Cannot use the existing parser: its PHP version is different than the one requested.';

        if (null === $versionUsed) {
            Assert::null($versionRequest, $parserMessage);
        } else {
            Assert::notNull($versionRequest, $parserMessage);
            Assert::true($versionUsed->equals($versionRequest), $parserMessage);
        }
    }
}
