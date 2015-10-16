<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Tests\Handler\Util;

use Webmozart\Console\IO\BufferedIO;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class NormalizedLineEndingsIO extends BufferedIO
{
    public function fetchOutput()
    {
        return str_replace(PHP_EOL, "\n", parent::fetchOutput());
    }

    public function fetchErrors()
    {
        return str_replace(PHP_EOL, "\n", parent::fetchErrors());
    }
}
