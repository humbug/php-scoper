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

use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PatchScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\SymfonyScoper;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Filesystem\Filesystem;

final class Container
{
    private Filesystem $filesystem;
    private ConfigurationFactory $configFactory;
    private Parser $parser;
    private Scoper $scoper;

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
            );
        }

        return $this->configFactory;
    }

    public function getScoper(): Scoper
    {
        if (!isset($this->scoper)) {
            $this->scoper = new PatchScoper(
                new PhpScoper(
                    $this->getParser(),
                    new JsonFileScoper(
                        new InstalledPackagesScoper(
                            new SymfonyScoper(
                                new NullScoper()
                            )
                        )
                    ),
                    new TraverserFactory(new Reflector())
                )
            );
        }

        return $this->scoper;
    }

    public function getParser(): Parser
    {
        if (!isset($this->parser)) {
            $this->parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7, new Lexer());
        }

        return $this->parser;
    }
}
