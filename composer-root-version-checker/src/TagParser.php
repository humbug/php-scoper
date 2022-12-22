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

use JsonException;
use function current;
use function get_debug_type;
use function is_array;
use function is_string;
use function Safe\json_decode;
use function Safe\json_encode;
use function sprintf;
use function trim;
use const JSON_PRETTY_PRINT;

final class TagParser
{
    public static function parse(string $responseContent): string
    {
        try {
            $decodedContent = json_decode(
                $responseContent,
                false,
                512,
                JSON_PRETTY_PRINT & JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            throw CouldNotParseTag::noTagFound($responseContent);
        }

        if (!is_array($decodedContent)) {
            throw CouldNotParseTag::noTagFound($responseContent);
        }

        $lastReleaseInfo = current($decodedContent);

        if (false === $lastReleaseInfo) {
            throw CouldNotParseTag::noTagFound($responseContent);
        }

        if (!isset($lastReleaseInfo->name)) {
            throw CouldNotParseTag::noNameTagFound(json_encode($lastReleaseInfo, JSON_PRETTY_PRINT));
        }

        $tagName = $lastReleaseInfo->name;

        if (!is_string($tagName)) {
            throw CouldNotParseTag::withReason(
                $tagName,
                sprintf(
                    'Expected the tag to be a non-blank string, got "%s".',
                    get_debug_type($tagName),
                ),
            );
        }

        $lastRelease = trim($tagName);

        if ('' === $lastRelease) {
            throw CouldNotParseTag::withReason(
                $tagName,
                'Expected the tag to be a non-blank string, got an empty string.',
            );
        }

        return $lastRelease;
    }

    private function __construct()
    {
    }
}
