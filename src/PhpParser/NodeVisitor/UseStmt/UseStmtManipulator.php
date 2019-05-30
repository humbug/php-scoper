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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * @private
 */
final class UseStmtManipulator extends NodeVisitorAbstract
{
    private const ORIGINAL_NAME_ATTRIBUTE = 'originalName';

    public static function hasOriginalName(UseUse $use): bool
    {
        return $use->hasAttribute(self::ORIGINAL_NAME_ATTRIBUTE);
    }

    public static function getOriginalName(UseUse $use): ?Name
    {
        if (false === self::hasOriginalName($use)) {
            return $use->name;
        }

        return $use->getAttribute(self::ORIGINAL_NAME_ATTRIBUTE);
    }

    public static function setOriginalName(UseUse $use, ?Name $originalName): void
    {
        $use->setAttribute(self::ORIGINAL_NAME_ATTRIBUTE, $originalName);
    }

    private function __construct()
    {
    }
}
