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

use function array_reduce;

final class PatcherChain implements Patcher
{
    /**
     * @var array<(callable(string, string, string): string)|Patcher>
     */
    private array $patchers;

    /**
     * @param array<(callable(string, string, string): string)|Patcher> $patchers
     */
    public function __construct(array $patchers = [])
    {
        $this->patchers = $patchers;
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
     * @internal
     *
     * @return array<(callable(string, string, string): string)|Patcher>
     */
    public function getPatchers(): array
    {
        return $this->patchers;
    }
}
