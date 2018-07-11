<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\Node;


use PhpParser\Node\Name\FullyQualified;

final class PrefixedName extends FullyQualified
{
    private $prefixedName;
    private $originalName;

    /**
     * @inheritdoc
     */
    public function __construct(FullyQualified $prefixedName, FullyQualified $originalName, array $attributes = [])
    {
        parent::__construct($prefixedName, $attributes);

        $this->prefixedName = new FullyQualified($prefixedName, $attributes);
        $this->originalName = new FullyQualified($originalName, $attributes);
    }

    public function getPrefixedName(): FullyQualified
    {
        return $this->prefixedName;
    }

    public function getOriginalName(): FullyQualified
    {
        return $this->originalName;
    }
}