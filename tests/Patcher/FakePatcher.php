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

namespace Humbug\PhpScoper\Patcher;

use LogicException;

final class FakePatcher implements Patcher
{
    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        throw new LogicException('Did not expect to be called');
    }
}
