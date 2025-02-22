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

use function array_reduce;

/**
 * @phpstan-import-type PatcherCallable from Patcher
 */
final readonly class PatcherChain implements Patcher
{
    /**
     * @param array<PatcherCallable|Patcher> $patchers
     */
    public function __construct(private array $patchers = [])
    {
    }

    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        return array_reduce(
            $this->patchers,
            static fn (string $contents, callable $patcher) => $patcher($filePath, $prefix, $contents),
            $contents,
        );
    }

    /**
     * @return array<PatcherCallable|Patcher>
     */
    public function getPatchers(): array
    {
        return $this->patchers;
    }
}
