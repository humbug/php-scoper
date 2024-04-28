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
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use function array_values;
use function count;
use function serialize;
use function unserialize;

final class SymbolsRegistry implements Countable
{
    /**
     * @var array<string, string>
     */
    private array $recordedAmbiguousFunctions = [];

    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedFunctionDeclarations = [];

    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedClasses = [];

    /**
     * @param array<array{string|FullyQualified, string|FullyQualified}> $functions
     * @param array<array{string|FullyQualified, string|FullyQualified}> $classes
     */
    public static function create(
        array $functions = [],
        array $classes = [],
    ): self {
        $registry = new self();

        foreach ($functions as [$original, $alias]) {
            $registry->recordAmbiguousFunctionCall(
                $original instanceof FullyQualified ? $original : new FullyQualified($original),
                $alias instanceof FullyQualified ? $alias : new FullyQualified($alias),
            );
        }

        foreach ($classes as [$original, $alias]) {
            $registry->recordClass(
                $original instanceof FullyQualified ? $original : new FullyQualified($original),
                $alias instanceof FullyQualified ? $alias : new FullyQualified($alias),
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
        foreach ($symbolsRegistry->getRecordedAmbiguousFunctions() as [$original, $alias]) {
            $this->recordedAmbiguousFunctions[$original] = [$original, $alias];
        }

        foreach ($symbolsRegistry->getRecordedClasses() as [$original, $alias]) {
            $this->recordedClasses[$original] = [$original, $alias];
        }
    }

    public function recordAmbiguousFunctionCall(Name $original): void
    {
        $this->recordedAmbiguousFunctions[(string) $original] = (string) $original;
    }

    /**
     * @return list<string>
     */
    public function getRecordedAmbiguousFunctions(): array
    {
        return array_values($this->recordedAmbiguousFunctions);
    }

    public function recordFunctionDeclaration(Name $original, FullyQualified $alias): void
    {
        $this->recordedFunctionDeclarations[(string) $original] = [(string) $original, (string) $alias];
    }

    /**
     * @return list<array{string, string}>
     */
    public function getRecordedFunctionDeclarations(): array
    {
        return array_values($this->recordedFunctionDeclarations);
    }

    public function recordClass(FullyQualified $original, FullyQualified $alias): void
    {
        $this->recordedClasses[(string) $original] = [(string) $original, (string) $alias];
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
        return count($this->recordedAmbiguousFunctions) + count($this->recordedClasses);
    }
}
