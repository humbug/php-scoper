<?php

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage;

use _PhpScoper5b2c11ee6df50\SebastianBergmann\Version as VersionId;
final class Version
{
    /**
     * @var string
     */
    private static $version;
    public static function id() : string
    {
        if (self::$version === null) {
            $version = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Version('6.0.7', \dirname(__DIR__));
            self::$version = $version->getVersion();
        }
        return self::$version;
    }
}
