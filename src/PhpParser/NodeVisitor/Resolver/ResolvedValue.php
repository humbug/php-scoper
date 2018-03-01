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

use PhpParser\Node\Name;

/**
 * @private
 */
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
