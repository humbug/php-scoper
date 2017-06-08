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

use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\Command\AddPrefixCommand;
use Humbug\PhpScoper\Handler\HandleAddPrefix;
use PackageVersions\Versions;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * @private
 */
function create_application(): SymfonyApplication
{
    $app = new Application('PHP Scoper', get_version());

    $app->addCommands([
        new AddPrefixCommand(
            new HandleAddPrefix(
                new Scoper(
                    create_parser()
                )
            )
        ),
    ]);

    return $app;
}

/**
 * @private
 */
function get_version(): string
{
    $rawVersion = Versions::getVersion('humbug/php-scoper');

    list($prettyVersion, $commitHash) = explode('@', $rawVersion);

    return (1 === preg_match('/9{7}/', $prettyVersion)) ? $commitHash : $prettyVersion;
}

/**
 * @private
 */
function create_parser(): Parser
{
    return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
}
