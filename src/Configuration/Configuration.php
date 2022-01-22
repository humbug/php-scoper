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
use Humbug\PhpScoper\Whitelist;
use InvalidArgumentException;
use function Safe\preg_match;
use function Safe\sprintf;

final class Configuration
{
    private const PREFIX_PATTERN = '/^[\p{L}\d_\\\\]+$/u';

    private ?string $path;
    private string $prefix;
    private array $filesWithContents;
    private array $whitelistedFilesWithContents;
    private Patcher $patcher;
    private SymbolsConfiguration $symbolsConfiguration;

    /**
     * @var string[]
     */
    private array $internalClasses;

    /**
     * @var string[]
     */
    private array $internalFunctions;

    /**
     * @var string[]
     */
    private array $internalConstants;

    /**
     * @param string|null                          $path                         Absolute path to the configuration file loaded.
     * @param string                               $prefix                       The prefix applied.
     * @param array<string, array{string, string}> $filesWithContents            Array of tuple with the
     *                                            first argument being the file path and the second
     *                                            its contents
     * @param array<string, array{string, string}> $whitelistedFilesWithContents Array of tuple
     *                                            with the first argument being the file path and
     *                                            the second its contents
     * @param SymbolsConfiguration                 $symbolsConfiguration
     * @param string[]                             $internalClasses
     * @param string[]                             $internalFunctions
     * @param string[]                             $internalConstants
     */
    public function __construct(
        ?string $path,
        string $prefix,
        array $filesWithContents,
        array $whitelistedFilesWithContents,
        Patcher $patcher,
        SymbolsConfiguration $symbolsConfiguration,
        array $internalClasses,
        array $internalFunctions,
        array $internalConstants
    ) {
        self::validatePrefix($prefix);

        $this->path = $path;
        $this->prefix = $prefix;
        $this->filesWithContents = $filesWithContents;
        $this->patcher = $patcher;
        $this->symbolsConfiguration = $symbolsConfiguration;
        $this->whitelistedFilesWithContents = $whitelistedFilesWithContents;
        $this->internalClasses = $internalClasses;
        $this->internalFunctions = $internalFunctions;
        $this->internalConstants = $internalConstants;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function getFilesWithContents(): array
    {
        return $this->filesWithContents;
    }

    public function getPatcher(): Patcher
    {
        return $this->patcher;
    }

    public function getSymbolsConfiguration(): SymbolsConfiguration
    {
        return $this->symbolsConfiguration;
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function getWhitelistedFilesWithContents(): array
    {
        return $this->whitelistedFilesWithContents;
    }

    /**
     * @return string[]
     */
    public function getInternalClasses(): array
    {
        return $this->internalClasses;
    }

    /**
     * @return string[]
     */
    public function getInternalFunctions(): array
    {
        return $this->internalFunctions;
    }

    /**
     * @return string[]
     */
    public function getInternalConstants(): array
    {
        return $this->internalConstants;
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
