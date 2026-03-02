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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Configuration\Throwable\InvalidConfigurationValue;
use function Safe\preg_match;

final class PrefixValidator
{
    private const PREFIX_PATTERN = '/^[\p{L}\d_\\\]+$/u';

    /**
     * @phpstan-assert non-empty-string $prefix
     *
     * @throws InvalidConfigurationValue
     */
    public static function validate(string $prefix): void
    {
        if (1 !== preg_match(self::PREFIX_PATTERN, $prefix)) {
            throw InvalidConfigurationValue::forInvalidPrefixPattern($prefix);
        }

        if (preg_match('/\\\{2,}/', $prefix)) {
            throw InvalidConfigurationValue::forInvalidNamespaceSeparator($prefix);
        }
    }
}
