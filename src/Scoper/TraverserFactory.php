<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\NodeVisitor\FullyQualifiedNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\FunctionCallScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\GlobalWhitelistedNamesNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\GroupUseNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\IgnoreNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\NamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\ParentNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\SingleLevelUseAliasVisitor;
use Humbug\PhpScoper\NodeVisitor\UseNamespaceScoperNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

/**
 * @internal
 */
final class TraverserFactory
{
    private $traverser;
    
    public function create(string $prefix, array $whitelist, callable $globalWhitelister): NodeTraverserInterface
    {
        if (null !== $this->traverser) {
            return $this->traverser;
        }

        $this->traverser = new NodeTraverser();

        $this->traverser->addVisitor(new ParentNodeVisitor());
        $this->traverser->addVisitor(new SingleLevelUseAliasVisitor($prefix));
        $this->traverser->addVisitor(new IgnoreNamespaceScoperNodeVisitor($globalWhitelister));
        $this->traverser->addVisitor(new GroupUseNamespaceScoperNodeVisitor($prefix));
        $this->traverser->addVisitor(new NamespaceScoperNodeVisitor($prefix));
        $this->traverser->addVisitor(new FunctionCallScoperNodeVisitor($prefix, ['class_exists', 'interface_exists']));
        $this->traverser->addVisitor(new UseNamespaceScoperNodeVisitor($prefix));
        $this->traverser->addVisitor(new FullyQualifiedNodeVisitor($prefix));
        $this->traverser->addVisitor(new GlobalWhitelistedNamesNodeVisitor($prefix, $globalWhitelister));

        return $this->traverser;
    }
}