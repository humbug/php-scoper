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

namespace Humbug\PhpScoper\Scoper\Spec;

use DomainException;
use Throwable;
use function sprintf;

final class UnparsableSpec extends DomainException
{
    public static function create(
        string $specTitle,
        Throwable $throwable,
    ): self {
        return new self(
            sprintf(
                'Could not parse the spec "%s": %s',
                $specTitle,
                $throwable->getMessage(),
            ),
            previous: $throwable,
        );
    }
}
