<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

use function Safe\sprintf;

final class DummyPatcher implements Patcher
{
    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        return sprintf('patchedContent<%s>', $contents);
    }
}
