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

namespace Humbug\PhpScoper\Console;

use Humbug\PhpScoper\Console\Command\AddPrefixCommand;
use Humbug\PhpScoper\Console\Command\InitCommand;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\Scoper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @final
 * TODO: mark this class as final in the next release
 */
class ApplicationFactory
{
    public function create(): Application
    {
        $app = new Application(
            new Container(),
            'PHP Scoper'
        );

        $app->addCommands([
            new AddPrefixCommand(
                new Filesystem(),
                $app->getContainer()->getScoper()
            ),
            new InitCommand(),
        ]);

        return $app;
    }

    /**
     * @deprecated This function will be removed in the next release
     */
    protected static function createScoper(): Scoper
    {
        return (new Container())->getScoper();
    }
}
