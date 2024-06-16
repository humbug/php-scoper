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

use Stringable;

final readonly class Prefix implements Stringable
{
    public function __construct(private string $prefix)
    {
        PrefixValidator::validate($this->prefix);
    }

    public function __toString(): string
    {
        return $this->prefix;
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
