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

use function array_flip;
use function array_key_exists;
use function func_get_args;

final class FileWhitelistScoper implements Scoper
{
    private readonly array $filePaths;

    public function __construct(
        private readonly Scoper $decoratedScoper,
        string ...$filePaths,
    ) {
        $this->filePaths = array_flip($filePaths);
    }

    public function scope(string $filePath, string $contents): string
    {
        if (array_key_exists($filePath, $this->filePaths)) {
            return $contents;
        }

        return $this->decoratedScoper->scope(...func_get_args());
    }
}
