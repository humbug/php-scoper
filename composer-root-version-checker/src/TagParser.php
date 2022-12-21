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

namespace Humbug\PhpScoperComposerRootChecker;

use RuntimeException;
use function current;
use function is_array;
use function is_string;
use function Safe\json_decode;
use function sprintf;
use function trim;

final class TagParser
{
    public static function parse(string $responseContent): string
    {
        $decodedContent = json_decode(
            $responseContent,
            false,
            512,
            JSON_PRETTY_PRINT & JSON_THROW_ON_ERROR,
        );

        if (!is_array($decodedContent)) {
            throw new RuntimeException(
                sprintf(
                    'No tag name could be found in: %s',
                    $responseContent,
                ),
                100,
            );
        }

        $lastReleaseInfo = current($decodedContent);

        if (false === $lastReleaseInfo) {
            throw new RuntimeException(
                sprintf(
                    'No tag name could be found in: %s',
                    $responseContent,
                ),
                100,
            );
        }

        if (!($lastReleaseInfo->name) || !is_string($lastReleaseInfo->name)) {
            throw new RuntimeException(
                sprintf(
                    'No tag name could be found in: %s',
                    $responseContent,
                ),
                100,
            );
        }

        $lastRelease = trim($lastReleaseInfo->name);

        if ('' === $lastRelease) {
            throw new RuntimeException('Invalid tag name found.');
        }

        return $lastRelease;
    }

    private function __construct()
    {
    }
}
