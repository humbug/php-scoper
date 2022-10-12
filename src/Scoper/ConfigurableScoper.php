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

use function count;
use function func_get_args;

/**
 * @deprecated since 0.16. Will be removed in 0.17.
 */
final class ConfigurableScoper implements Scoper
{
    private Scoper $decoratedScoper;

    public function __construct(Scoper $decoratedScoper)
    {
        $this->decoratedScoper = $decoratedScoper;
    }

    public function withWhitelistedFiles(string ...$whitelistedFiles): self
    {
        return count($whitelistedFiles) === 0
            ? $this
            : new self(
                new FileWhitelistScoper(
                    clone $this,
                    ...$whitelistedFiles
                )
            );
    }

    public function scope(string $filePath, string $contents): string
    {
        return $this->decoratedScoper->scope(...func_get_args());
    }
}
