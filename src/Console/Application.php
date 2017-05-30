<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Console;

use Silly\Application as SillyApplication;

class Application extends SillyApplication
{
    /**
     * The version of the PHP-Scoper.
     */
    const VERSION = '@package_version@';

    public function __construct()
    {
        if (Application::VERSION == '@package_version@') {
            $version = '1.0-dev';
        } else {
            $version = Application::VERSION;
        }
        
        parent::__construct(
            'php-scoper',
            $version
        );
    }
}
