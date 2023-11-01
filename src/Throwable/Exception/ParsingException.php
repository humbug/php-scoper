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

namespace Humbug\PhpScoper\Throwable\Exception;

use Throwable;

final class ParsingException extends RuntimeException
{
    public static function forFile(string $filePath, Throwable $previous): self
    {
        return new self(
            sprintf(
                'Could not parse the file "%s".',
                $filePath,
            ),
            previous: $previous,
        );
    }
}
