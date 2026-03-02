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

use Humbug\PhpScoper\Configuration\Throwable\InvalidConfigurationValue;
use Humbug\PhpScoper\Patcher\Patcher;
use PhpParser\PhpVersion;

final class Configuration
{
    private readonly Prefix $prefix;

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
     *
     * @throws InvalidConfigurationValue
     */
    public function __construct(
        private ?string $path,
        private ?string $outputDir,
        string|Prefix $prefix,
        private ?PhpVersion $phpVersion,
        private array $filesWithContents,
        private array $excludedFilesWithContents,
        private Patcher $patcher,
        private SymbolsConfiguration $symbolsConfiguration,
    ) {
        $this->prefix = $prefix instanceof Prefix
            ? $prefix
            : new Prefix($prefix);
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
     *
     * @throws InvalidConfigurationValue
     */
    public function withPrefix(string $prefix): self
    {
        return new self(
            $this->path,
            $this->outputDir,
            $prefix,
            $this->phpVersion,
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
        return $this->prefix->toString();
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
            $this->phpVersion,
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
            $this->phpVersion,
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

    public function getPhpVersion(): ?PhpVersion
    {
        return $this->phpVersion;
    }
}
