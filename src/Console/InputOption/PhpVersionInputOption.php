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

namespace Humbug\PhpScoper\Console\InputOption;

use Fidry\Console\IO;
use Humbug\PhpScoper\NotInstantiable;
use PhpParser\PhpVersion;
use Symfony\Component\Console\Input\InputOption;

/**
 * @private
 */
final class PhpVersionInputOption
{
    use NotInstantiable;

    private const PHP_VERSION_OPT = 'php-version';

    public static function createInputOption(): InputOption
    {
        return new InputOption(
            self::PHP_VERSION_OPT,
            null,
            InputOption::VALUE_REQUIRED,
            'PHP version in which the PHP parser and printer will be configured, e.g. "7.2".',
        );
    }

    public static function getPhpVersion(IO $io): ?PhpVersion
    {
        $version = $io
            ->getTypedOption(self::PHP_VERSION_OPT)
            ->asNullableString();

        return null === $version
            ? $version
            : PhpVersion::fromString($version);
    }
}
