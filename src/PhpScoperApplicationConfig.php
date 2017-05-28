<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper;

use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PhpScoper\Handler\AddPrefixCommandHandler;

/**
 * The configuration of the PHP-Scoper CLI.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PhpScoperApplicationConfig
{
    public static function configure(PhpScoperApplication $app)
    {
        $app->command(
            'add-prefix prefix path*',
            function ($prefix, $path, OutputInterface $output) {
                $handler = new AddPrefixCommandHandler;
                $handler->handle($prefix, $path, $output);
            }
        );
        $app->setDefaultCommand('add-prefix');
    }
}
