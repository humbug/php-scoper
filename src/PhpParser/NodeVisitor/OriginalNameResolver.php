<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class OriginalNameResolver
{
    private const ORIGINAL_NAME_ATTRIBUTE = 'originalName';

    public static function hasOriginalName(Name $namespace): bool
    {
        return $namespace->hasAttribute(self::ORIGINAL_NAME_ATTRIBUTE);
    }

    /**
     * @return Name|Identifier
     */
    public static function getOriginalName(Name $name): Node
    {
        if (false === self::hasOriginalName($name)) {
            return $name;
        }

        return $name->getAttribute(self::ORIGINAL_NAME_ATTRIBUTE);
    }

    public static function setOriginalName(Name $namespace, ?Name $originalName): void
    {
        $namespace->setAttribute(self::ORIGINAL_NAME_ATTRIBUTE, $originalName);
    }

    private function __construct()
    {
    }
}
