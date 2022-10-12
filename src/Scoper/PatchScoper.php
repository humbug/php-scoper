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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Patcher\Patcher;
use function func_get_args;

final class PatchScoper implements Scoper
{
    private Scoper $decoratedScoper;
    private string $prefix;
    private Patcher $patcher;

    public function __construct(Scoper $decoratedScoper, string $prefix, Patcher $patcher)
    {
        $this->decoratedScoper = $decoratedScoper;
        $this->prefix = $prefix;
        $this->patcher = $patcher;
    }

    public function scope(string $filePath, string $contents): string
    {
        return ($this->patcher)(
            $filePath,
            $this->prefix,
            $this->decoratedScoper->scope(...func_get_args()),
        );
    }
}
