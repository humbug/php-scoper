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
use function sprintf;
use const PHP_EOL;

final class CouldNotParseTag extends RuntimeException
{
    public static function noTagFound(
        string $content
    ): self {
        return new self(
            sprintf(
                'No tag could be found in: "%s".',
                $content,
            ),
        );
    }

    public static function noNameTagFound(
        string $content
    ): self {
        return new self(
            sprintf(
                'No tag name could be found in:%s"%s".',
                PHP_EOL,
                $content,
            ),
        );
    }

    public static function withReason(
        null|bool|int|float|string $tag,
        string $reason
    ): self {
        return new self(
            sprintf(
                'Could not parse the tag "%s": %s',
                $tag,
                $reason,
            ),
        );
    }
}
