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

namespace Humbug\PhpScoper\Configuration\Throwable;

use UnexpectedValueException;
use function get_debug_type;

final class InvalidConfigurationFile extends UnexpectedValueException implements InvalidConfiguration
{
    public static function forNonAbsolutePath(string $path): self
    {
        return new self(
            sprintf(
                'Expected the path of the configuration file to load to be an absolute path, got "%s" instead',
                $path,
            ),
        );
    }

    public static function forFileNotFound(string $path): self
    {
        return new self(
            sprintf(
                'Expected the path of the configuration file to exists but the file "%s" could not be found',
                $path,
            ),
        );
    }

    public static function forNotAFile(string $path): self
    {
        return new self(
            sprintf(
                'Expected the path of the configuration file to be a file but "%s" appears to be a directory.',
                $path,
            ),
        );
    }

    public static function forInvalidValue(mixed $config): self
    {
        return new self(
            sprintf(
                'Expected configuration to be an array, found "%s" instead.',
                get_debug_type($config),
            ),
        );
    }
}
