<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor\Resolver;

use PhpParser\Node\Name;

final class ResolvedValue
{
    private $name;
    private $namespace;
    private $use;

    public function __construct(Name $name, ?Name $namespace, ?Name $use)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->use = $use;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getNamespace(): ?Name
    {
        return $this->namespace;
    }

    public function getUse(): ?Name
    {
        return $this->use;
    }
}