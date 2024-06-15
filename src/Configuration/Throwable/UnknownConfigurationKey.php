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

final class UnknownConfigurationKey extends UnexpectedValueException implements InvalidConfiguration
{
    public static function forKey(string $key): self
    {
        return new self(
            sprintf(
                'Invalid configuration key value "%s" found.',
                $key,
            ),
        );
    }
}
