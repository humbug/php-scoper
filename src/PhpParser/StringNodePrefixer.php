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

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Error as PhpParserError;
use PhpParser\Node\Scalar\String_;
use function Safe\substr;

/**
 * @private
 */
final class StringNodePrefixer
{
    private Scoper $scoper;
    private string $prefix;
    private Whitelist $whitelist;

    public function __construct(Scoper $scoper, string $prefix, Whitelist $whitelist)
    {
        $this->scoper = $scoper;
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
    }

    public function prefixStringValue(String_ $node): void
    {
        try {
            $lastChar = substr($node->value, -1);

            $newValue = $this->scoper->scope(
                '',
                $node->value,
                $this->prefix,
                [],
                $this->whitelist,
            );

            if ("\n" !== $lastChar) {
                $newValue = substr($newValue, 0, -1);
            }

            $node->value = $newValue;
        } catch (PhpParserError $error) {
            // Continue without scoping the heredoc which for some reasons contains invalid PHP code
        }
    }
}
