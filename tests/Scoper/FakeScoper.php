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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Scoper;
use LogicException;

final class FakeScoper implements Scoper
{
    /**
     * @inheritdoc
     */
    public function scope(string $filePath, string $prefix, array $patchers, array $whitelist, callable $globalWhitelister): string
    {
        throw new LogicException();
    }
}
