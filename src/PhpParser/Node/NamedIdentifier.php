<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

/**
 * Small wrapper to treat an identifier as a name node.
 */
final class NamedIdentifier extends Name
{
    private $originalNode;

    public static function create(Identifier $node): self
    {
        $instance = new self($node->name, $node->getAttributes());
        $instance->originalNode = $node;

        return $instance;
    }

    public function getOriginalNode(): Identifier
    {
        return $this->originalNode;
    }
}