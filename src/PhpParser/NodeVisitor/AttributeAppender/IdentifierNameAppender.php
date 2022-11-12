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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender;

use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeVisitorAbstract;

/**
 * In some contexts we need to resolve identifiers but they can no longer be
 * resolved on the fly. For those, we store the resolved identifier as an
 * attribute.
 *
 * @see ClassAliasStmtAppender
 *
 * @private
 */
final class IdentifierNameAppender extends NodeVisitorAbstract
{
    public function __construct(private readonly IdentifierResolver $identifierResolver)
    {
    }

    public function enterNode(Node $node): ?Node
    {
        if (!($node instanceof Class_ || $node instanceof Interface_)) {
            return null;
        }

        $name = $node->name;

        if (null === $name) {
            return null;
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($name);

        $name->setAttribute('resolvedName', $resolvedName);

        return null;
    }
}
