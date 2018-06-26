<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Runner;

use _PhpScoper5b2c11ee6df50\SebastianBergmann\Version as VersionId;
/**
 * This class defines the current version of PHPUnit.
 */
class Version
{
    private static $pharVersion;
    private static $version;
    /**
     * Returns the current version of PHPUnit.
     */
    public static function id() : string
    {
        if (self::$pharVersion !== null) {
            return self::$pharVersion;
        }
        if (self::$version === null) {
            $version = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Version('7.2.4', \dirname(__DIR__, 2));
            self::$version = $version->getVersion();
        }
        return self::$version;
    }
    public static function series() : string
    {
        if (\strpos(self::id(), '-')) {
            $version = \explode('-', self::id())[0];
        } else {
            $version = self::id();
        }
        return \implode('.', \array_slice(\explode('.', $version), 0, 2));
    }
    public static function getVersionString() : string
    {
        return 'PHPUnit ' . self::id() . ' by Sebastian Bergmann and contributors.';
    }
    public static function getReleaseChannel() : string
    {
        if (\strpos(self::$pharVersion, '-') !== \false) {
            return '-nightly';
        }
        return '';
    }
}
