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
use function array_intersect_key;
use function array_merge;
use function array_sum;
use function array_values;
use function count;
use function serialize;
use function unserialize;

final class SymbolsRegistry implements Countable
{
    /**
     * @var array<string, string>
     */
    private array $recordedAmbiguousFunctionCalls = [];

    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedFunctionDeclarations = [];

    /**
     * @var list<array{string, string}>
     */
    private array $recordedFunctions;

    /**
     * @var array<string, array{string, string}>
     */
    private array $recordedClasses = [];

    /**
     * @param array<array{string|FullyQualified, string|FullyQualified}> $functionDeclarations
     * @param array<string|Name> $ambiguousFunctionCalls
     * @param array<array{string|FullyQualified, string|FullyQualified}> $classes
     */
    public static function create(
        array $functionDeclarations = [],
        array $ambiguousFunctionCalls = [],
        array $classes = [],
    ): self {
        $registry = new self();

        foreach ($functionDeclarations as [$original, $alias]) {
            $registry->recordFunctionDeclaration(
                self::toFQN($original),
                self::toFQN($alias),
            );
        }

        foreach ($ambiguousFunctionCalls as $ambiguousFunctionCall) {
            $registry->recordAmbiguousFunctionCall(self::toFQN($ambiguousFunctionCall));
        }

        foreach ($classes as [$original, $alias]) {
            $registry->recordClass(
                self::toFQN($original),
                self::toFQN($alias),
            );
        }

        return $registry;
    }

    private static function toFQN(string|Name $value): FullyQualified
    {
        return $value instanceof FullyQualified ? $value : new FullyQualified($value);
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
        $this->recordedFunctionDeclarations = array_merge(
            $this->recordedFunctionDeclarations,
            $symbolsRegistry->recordedFunctionDeclarations,
        );
        $this->recordedAmbiguousFunctionCalls = array_merge(
            $this->recordedAmbiguousFunctionCalls,
            $symbolsRegistry->recordedAmbiguousFunctionCalls,
        );
        $this->recordedClasses = array_merge(
            $this->recordedClasses,
            $symbolsRegistry->recordedClasses,
        );
        unset($this->recordedFunctions);
    }

    // TODO: should only record func call that are:
    //  - ambiguous
    //  - where the non prefixed namespace is:
    //      - not internal
    //      - exposed
    //  - should record the prefix name
    public function recordAmbiguousFunctionCall(Name $original): void
    {
        $this->recordedAmbiguousFunctionCalls[(string) $original] = (string) $original;
        unset($this->recordedFunctions);
    }

    /**
     * @return list<string>
     */
    public function getRecordedAmbiguousFunctionCalls(): array
    {
        return array_values($this->recordedAmbiguousFunctionCalls);
    }

    // TODO: should record all function declarations, namespaced or not
    public function recordFunctionDeclaration(FullyQualified $original, FullyQualified $alias): void
    {
        $this->recordedFunctionDeclarations[(string) $alias] = [(string) $original, (string) $alias];
        unset($this->recordedFunctions);
    }

    /**
     * @return list<array{string, string}>
     */
    public function getRecordedFunctionDeclarations(): array
    {
        return array_values($this->recordedFunctionDeclarations);
    }

    /**
     * @return list<array{string, string}>
     */
    public function getRecordedFunctions(): array
    {
        if (isset($this->recordedFunctions)) {
            return $this->recordedFunctions;
        }

        $this->recordedFunctions = array_values(
            array_intersect_key(
                $this->recordedFunctionDeclarations,
                $this->recordedAmbiguousFunctionCalls,
            ),
        );

        return $this->recordedFunctions;
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
        return count($this->recordedFunctionDeclarations) + count($this->getRecordedFunctions();
    }
}
