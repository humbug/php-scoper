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

use Webmozart\Console\Api\Args\Format\Argument;
use Webmozart\Console\Config\DefaultApplicationConfig;
use Webmozart\PhpScoper\Handler\AddPrefixCommandHandler;

/**
 * The configuration of the PHP-Scoper CLI.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PhpScoperApplicationConfig extends DefaultApplicationConfig
{
    /**
     * The version of the PHP-Scoper.
     */
    const VERSION = '@package_version@';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('php-scoper')
            ->setDisplayName('PHP-Scoper')
            ->setVersion(self::VERSION)

            // Enable debug for unreleased versions only. Split the string to
            // prevent its replacement during release
            ->setDebug('@pack'.'age_version@' === self::VERSION)
        ;

        $this
            ->beginCommand('add-prefix')
                ->addArgument('prefix', Argument::REQUIRED, 'The manespace prefix to add. Must end with a backslash.')
                ->addArgument('path', Argument::REQUIRED | Argument::MULTI_VALUED, 'The path(s) to process.')
                ->setHandler(function () {
                    return new AddPrefixCommandHandler();
                })
            ->end()
        ;
    }
}
