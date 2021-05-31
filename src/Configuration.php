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

namespace Humbug\PhpScoper;

use InvalidArgumentException;
use function Safe\preg_match;
use function Safe\sprintf;

final class Configuration
{
    private const PREFIX_PATTERN = '/^[\p{L}\d_\\\\]+$/u';

    private ?string $path;
    private string $prefix;
    private array $filesWithContents;
    private array $whitelistedFiles;
    private array $patchers;
    private Whitelist $whitelist;

    /**
     * @param string|null $path                   Absolute path to the configuration file loaded.
     * @param string      $prefix                 The prefix applied.
     * @param array<string, array{string, string}> $filesWithContents Array of tuple with the
     *                                            first argument being the file path and the second
     *                                            its contents
     * @param array<string, array{string, string}> $whitelistedFiles Array of tuple with the
     *                                            first argument being the file path and the second
     *                                            its contents
     * @param callable[]  $patchers               List of closures which can alter the content of
     *                                            the files being scoped.
     * @param Whitelist   $whitelist              List of classes that will not be scoped.
     *                                            returning a boolean which if `true` means the
     *                                            class should be scoped
     *                                            (i.e. is ignored) or scoped otherwise.
     */
    public function __construct(
        ?string $path,
        string $prefix,
        array $filesWithContents,
        array $whitelistedFiles,
        array $patchers,
        Whitelist $whitelist
    ) {
        self::validatePrefix($prefix);

        $this->path = $path;
        $this->prefix = $prefix;
        $this->filesWithContents = $filesWithContents;
        $this->patchers = $patchers;
        $this->whitelist = $whitelist;
        $this->whitelistedFiles = $whitelistedFiles;
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

    /**
     * @return callable[]
     */
    public function getPatchers(): array
    {
        return $this->patchers;
    }

    public function getWhitelist(): Whitelist
    {
        return $this->whitelist;
    }

    /**
     * @return string[]
     */
    public function getWhitelistedFiles(): array
    {
        return $this->whitelistedFiles;
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
