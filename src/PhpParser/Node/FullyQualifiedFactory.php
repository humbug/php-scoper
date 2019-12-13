<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\Node;

use InvalidArgumentException;
use PhpParser\Node\Name\FullyQualified;

final class FullyQualifiedFactory
{
    /**
     * @param string|string[]|self|null $name1
     * @param string|string[]|self|null $name2
     */
    public static function concat($name1, $name2, array $attributes = []): FullyQualified {
        if (null === $name1 && null === $name2) {
            throw new InvalidArgumentException('Expected one of the names to not be null');
        }

        /** @var FullyQualified $fqName */
        $fqName = FullyQualified::concat($name1, $name2, $attributes);

        return $fqName;
    }

    private function __construct()
    {
    }
}
