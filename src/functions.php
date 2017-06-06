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

use Humbug\PhpScoper\Console\Command\AddPrefixCommand;
use Humbug\PhpScoper\Handler\HandleAddPrefix;
use PackageVersions\Versions;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;

function createApplication(): Application
{
    $app = new Application('php-scoper', Versions::getVersion('humbug/php-scoper'));

    $app->addCommands([
        new AddPrefixCommand(
            new HandleAddPrefix(
                new Scoper(
                    createParser()
                )
            )
        ),
    ]);

    return $app;
}

function createParser(): Parser
{
    return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
}
