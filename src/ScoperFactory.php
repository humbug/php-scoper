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
use Humbug\PhpScoper\Scoper\Composer\AutoloadPrefixer;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PatchScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\SymfonyScoper;
use PhpParser\Parser;

/**
 * @final
 */
class ScoperFactory
{
    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function createScoper(Configuration $configuration): Scoper
    {
        $prefix = $configuration->getPrefix();
        $whitelist = $configuration->getWhitelist();

        $autoloadPrefix = new AutoloadPrefixer($prefix, $whitelist);

        return new PatchScoper(
            new PhpScoper(
                $this->parser,
                new JsonFileScoper(
                    new InstalledPackagesScoper(
                        new SymfonyScoper(
                            new NullScoper(),
                            $prefix,
                            $whitelist,
                        ),
                        $autoloadPrefix
                    ),
                    $autoloadPrefix
                ),
                new TraverserFactory(
                    Reflector::createWithPhpStormStubs()->withSymbols(
                        $configuration->getInternalClasses(),
                        $configuration->getInternalFunctions(),
                        $configuration->getInternalConstants(),
                    ),
                ),
                $prefix,
                $whitelist,
            ),
            $prefix,
            $configuration->getPatchers(),
        );
    }
}
