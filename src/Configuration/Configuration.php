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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Patcher\Patcher;
use InvalidArgumentException;
use function Safe\preg_match;
use function sprintf;

final class Configuration
{
    private const PREFIX_PATTERN = '/^[\p{L}\d_\\\\]+$/u';

    /**
     * @var non-empty-string
     */
    private readonly string $prefix;

    /**
     * @param non-empty-string|null                $path                      Absolute canonical path to the configuration file loaded.
     * @param non-empty-string|null                $outputDir                 Absolute canonical path to the output directory.
     * @param non-empty-string                     $prefix                    The prefix applied.
     * @param array<string, array{string, string}> $filesWithContents         Array of tuple with the
     *                                                                        first argument being the file path and the second
     *                                                                        its contents
     * @param array<string, array{string, string}> $excludedFilesWithContents Array of tuple
     *                                                                        with the first argument being the file path and
     *                                                                        the second its contents
     */
    public function __construct(
        private ?string $path,
        private ?string $outputDir,
        string $prefix,
        private array $filesWithContents,
        private array $excludedFilesWithContents,
        private Patcher $patcher,
        private SymbolsConfiguration $symbolsConfiguration
    ) {
        self::validatePrefix($prefix);

        $this->prefix = $prefix;
    }

    /**
     * @return non-empty-string|null Absolute canonical path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return non-empty-string|null Absolute canonical path
     */
    public function getOutputDir(): ?string
    {
        return $this->outputDir;
    }

    /**
     * @param non-empty-string $prefix
     */
    public function withPrefix(string $prefix): self
    {
        return new self(
            $this->path,
            $this->outputDir,
            $prefix,
            $this->filesWithContents,
            $this->excludedFilesWithContents,
            $this->patcher,
            $this->symbolsConfiguration,
        );
    }

    /**
     * @return non-empty-string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param array<string, array{string, string}> $filesWithContents
     */
    public function withFilesWithContents(array $filesWithContents): self
    {
        return new self(
            $this->path,
            $this->outputDir,
            $this->prefix,
            $filesWithContents,
            $this->excludedFilesWithContents,
            $this->patcher,
            $this->symbolsConfiguration,
        );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function getFilesWithContents(): array
    {
        return $this->filesWithContents;
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function getExcludedFilesWithContents(): array
    {
        return $this->excludedFilesWithContents;
    }

    public function withPatcher(Patcher $patcher): self
    {
        return new self(
            $this->path,
            $this->outputDir,
            $this->prefix,
            $this->filesWithContents,
            $this->excludedFilesWithContents,
            $patcher,
            $this->symbolsConfiguration,
        );
    }

    public function getPatcher(): Patcher
    {
        return $this->patcher;
    }

    public function getSymbolsConfiguration(): SymbolsConfiguration
    {
        return $this->symbolsConfiguration;
    }

    private static function validatePrefix(string $prefix): void
    {
        if (1 !== preg_match(self::PREFIX_PATTERN, $prefix)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The prefix needs to be composed solely of letters, digits and backslashes (as namespace separators). Got "%s"',
                    $prefix,
                ),
            );
        }

        if (preg_match('/\\\{2,}/', $prefix)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid namespace separator sequence. Got "%s"',
                    $prefix,
                ),
            );
        }
    }
}
