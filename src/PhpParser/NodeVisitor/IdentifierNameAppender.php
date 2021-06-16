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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use Humbug\PhpScoper\PhpParser\Node\ClassAliasFuncCall;
use Humbug\PhpScoper\PhpParser\Node\FullyQualifiedFactory;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use function array_reduce;

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
    private IdentifierResolver $identifierResolver;

    public function __construct(IdentifierResolver $identifierResolver)
    {
        $this->identifierResolver = $identifierResolver;
    }

    public function enterNode(Node $node): void
    {
        if (!($node instanceof Class_ || $node instanceof Interface_)) {
            return;
        }

        $name = $node->name;

        if (null === $name) {
            return;
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($node->name);

        $name->setAttribute('resolvedName', $resolvedName);
    }
}
