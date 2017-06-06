<?php

declare(strict_types=1);

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
