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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver;

use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Collection\UseStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NameStmtPrefixer;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use function in_array;

/**
 * Attempts to resolve the node name into a fully qualified node. Returns a valid (non fully-qualified) name node on
 * failure.
 *
 * @private
 */
final class FullyQualifiedNameResolver
{
    private $namespaceStatements;
    private $useStatements;

    public function __construct(NamespaceStmtCollection $namespaceStatements, UseStmtCollection $useStatements)
    {
        $this->namespaceStatements = $namespaceStatements;
        $this->useStatements = $useStatements;
    }

    /**
     * @param Name|Identifier
     */
    public function resolveName(Node $node): ResolvedValue
    {
        if ($node instanceof FullyQualified) {
            return new ResolvedValue($node, null, null);
        }

        if ($node instanceof Identifier) {
            $node = new Name($node->name, $node->getAttributes());
        }

        $namespaceName = $this->namespaceStatements->findNamespaceForNode($node);

        $useName = $this->useStatements->findStatementForNode($namespaceName, $node);

        return new ResolvedValue(
            $this->resolveNodeName($node, $namespaceName, $useName),
            $namespaceName,
            $useName
        );
    }

    private function resolveNodeName(Name $name, ?Name $namespace, ?Name $use): Name
    {
        if (null !== $use) {
            return FullyQualified::concat($use, $name->slice(1), $name->getAttributes());
        }

        if (null === $namespace) {
            return new FullyQualified($name, $name->getAttributes());
        }

        if (in_array((string) $name, NameStmtPrefixer::PHP_FUNCTION_KEYWORDS, true)) {
            return $name;
        }

        $parentNode = ParentNodeAppender::getParent($name);

        if (
            ($parentNode instanceof ConstFetch || $parentNode instanceof FuncCall)
            && 1 === count($name->parts)
        ) {
            // Ambiguous name, cannot determine the FQ name
            return $name;
        }

        return FullyQualified::concat($namespace, $name, $name->getAttributes());
    }
}
