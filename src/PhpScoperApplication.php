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

use Silly\Application as SillyApplication;

class PhpScoperApplication extends SillyApplication
{
    /**
     * The version of the PHP-Scoper.
     */
    const VERSION = '@package_version@';
}