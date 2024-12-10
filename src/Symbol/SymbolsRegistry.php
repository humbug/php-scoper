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
use PhpParser\Node\Name\FullyQualified;
use function array_map;
use function array_values;
use function count;
use function serialize;
use function unserialize;

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

    /**
     * @param array<array{string|FullyQualified, string|FullyQualified}> $functions
     * @param array<array{string|FullyQualified, string|FullyQualified, array<string|FullyQualified>}> $classes
     */
    public static function create(
        array $functions = [],
        array $classes = [],
    ): self {
        $registry = new self();

        foreach ($functions as [$original, $alias]) {
            $registry->recordFunction(
                $original instanceof FullyQualified ? $original : new FullyQualified($original),
                $alias instanceof FullyQualified ? $alias : new FullyQualified($alias),
            );
        }

        foreach ($classes as [$original, $alias, $dependencies]) {
            $registry->recordClass(
                $original instanceof FullyQualified ? $original : new FullyQualified($original),
                $alias instanceof FullyQualified ? $alias : new FullyQualified($alias),
                array_map(
                    static fn (string|FullyQualified $name) => $name instanceof FullyQualified ? $name : new FullyQualified($name),
                    $dependencies ?? [],
                ),
            );
        }

        return $registry;
    }

    /**
     * @param self[] $symbolsRegistries
     */
    public static function createFromRegistries(array $symbolsRegistries): self
    {
        $symbolsRegistry = new self();

        foreach ($symbolsRegistries as $symbolsRegistryToMerge) {
            $symbolsRegistry->merge($symbolsRegistryToMerge);
        }

        return $symbolsRegistry;
    }

    public static function unserialize(string $serialized): self
    {
        return unserialize(
            $serialized,
            ['allowed_classes' => [self::class]],
        );
    }

    public function serialize(): string
    {
        return serialize($this);
    }

    public function merge(self $symbolsRegistry): void
    {
        foreach ($symbolsRegistry->getRecordedFunctions() as [$original, $alias]) {
            $this->recordedFunctions[$original] = [$original, $alias];
        }

        foreach ($symbolsRegistry->getRecordedClasses() as [$original, $alias, $dependencies]) {
            $this->recordedClasses[$original] = [$original, $alias, $dependencies];
        }
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

    /**
     * @param FullyQualified[] $dependencies
     */
    public function recordClass(
        FullyQualified $original,
        FullyQualified $alias,
        array $dependencies = [],
    ): void
    {
        $this->recordedClasses[(string) $original] = [(string) $original, (string) $alias, $dependencies];
    }

    /**
     * @return array{string, string}|null
     */
    public function getRecordedClass(string $original): ?array
    {
        return $this->recordedClasses[$original] ?? null;
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
