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

use function str_contains;
use function strtolower;

final class VersionCalculator
{
    public static function calculateDesiredVersion(string $tag): string
    {
        $tagParts = explode('.', $tag);
        $desiredVersionParts = [];

        foreach ($tagParts as $tagPart) {
            $normalizedPart = strtolower($tagPart);

            if (str_contains($normalizedPart, 'rc')
                || str_contains($normalizedPart, 'alpha')
                || str_contains($normalizedPart, 'beta')
            ) {
                $desiredVersionParts[] = '99';

                return implode('.', $desiredVersionParts);
            }

            $desiredVersionParts[] = $tagPart;
        }

        array_pop($desiredVersionParts);

        $desiredVersionParts[] = '99';

        return implode('.', $desiredVersionParts);
    }

    private function __construct()
    {
    }
}
