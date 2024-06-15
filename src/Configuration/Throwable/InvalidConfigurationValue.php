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

namespace Humbug\PhpScoper\Configuration\Throwable;

use Symfony\Component\Finder\Finder;
use UnexpectedValueException;
use function gettype;

final class InvalidConfigurationValue extends UnexpectedValueException implements InvalidConfiguration
{
    public static function forInvalidPatchersType(mixed $patchers): self
    {
        return new self(
            sprintf(
                'Expected patchers to be an array of callables, found "%s" instead.',
                gettype($patchers),
            ),
        );
    }

    public static function forInvalidPatcherType(int|string $index, mixed $patcher): self
    {
        return new self(
            sprintf(
                'Expected patchers to be an array of callables, the "%s" element is not (found "%s" instead).',
                $index,
                gettype($patcher),
            ),
        );
    }

    public static function forInvalidExcludedFilesTypes(mixed $excludedFiles): self
    {
        return new self(
            sprintf(
                'Expected excluded files to be an array of strings, found "%s" instead.',
                gettype($excludedFiles),
            ),
        );
    }

    public static function forInvalidExcludedFilePath(int|string $index, mixed $excludedFile): self
    {
        return new self(
            sprintf(
                'Expected excluded files to be an array of string, the "%d" element is not (found "%s" instead).',
                $index,
                gettype($excludedFile),
            ),
        );
    }

    public static function forInvalidFinderTypes(mixed $finders): self
    {
        return new self(
            sprintf(
                'Expected finders to be an array of "%s", found "%s" instead.',
                Finder::class,
                gettype($finders),
            ),
        );
    }

    public static function forInvalidFinderType(int|string $index, mixed $finder): self
    {
        return new self(
            sprintf(
                'Expected finders to be an array of "%s", the "%s" element is not (found "%s" instead).',
                Finder::class,
                $index,
                gettype($finder),
            ),
        );
    }

    public static function forFileNotFound(string $path): self
    {
        return new self(
            sprintf(
                'Could not find the file "%s".',
                $path,
            ),
        );
    }

    public static function forUnreadableFile(string $path): self
    {
        return new self(
            sprintf(
                'Could not read the file "%s".',
                $path,
            ),
        );
    }

    public static function forInvalidPrefixPattern(string $prefix): self
    {
        return new self(
            sprintf(
                'The prefix needs to be composed solely of letters, digits and backslashes (as namespace separators). Got "%s".',
                $prefix,
            ),
        );
    }

    public static function forInvalidNamespaceSeparator(string $prefix): self
    {
        return new self(
            sprintf(
                'Invalid namespace separator sequence. Got "%s".',
                $prefix,
            ),
        );
    }
}
