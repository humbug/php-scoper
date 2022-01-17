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

namespace Humbug\PhpScoper\Symbol;

use Countable;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node\Name\FullyQualified;
use function array_values;
use function count;

final class SymbolsRegistry implements Countable
{
    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedFunctions = [];

    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedClasses = [];

    public static function fromWhitelist(Whitelist $whitelist): self
    {
        $registry = new self();

        foreach ($whitelist->getRecordedWhitelistedFunctions() as [$original, $alias]) {
            $registry->recordFunction(
                new FullyQualified($original),
                new FullyQualified($alias),
            );
        }

        foreach ($whitelist->getRecordedWhitelistedClasses() as [$original, $alias]) {
            $registry->recordClass(
                new FullyQualified($original),
                new FullyQualified($alias),
            );
        }

        return $registry;
    }
    
    public function recordFunction(FullyQualified $original, FullyQualified $alias): void
    {
        $this->recordedFunctions[(string) $original] = [(string) $original, (string) $alias];
    }

    /**
     * @return list<array{string, string}>
     */
    public function getRecordedFunctions(): array
    {
        return array_values($this->recordedFunctions);
    }

    public function recordClass(FullyQualified $original, FullyQualified $alias): void
    {
        $this->recordedClasses[(string) $original] = [(string) $original, (string) $alias];
    }

    /**
     * @return list<array{string, string}>
     */
    public function getRecordedClasses(): array
    {
        return array_values($this->recordedClasses);
    }

    public function count(): int
    {
        return count($this->recordedFunctions) + count($this->recordedClasses);
    }
}
