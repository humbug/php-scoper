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

use Humbug\PhpScoper\PhpParser\StringScoperPrefixer;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use function ltrim;
use function strpos;
use function substr;

final class NewdocPrefixer extends NodeVisitorAbstract
{
    use StringScoperPrefixer;

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof String_ && $this->isPhpNowdoc($node)) {
            $this->scopeStringValue($node);
        }

        return $node;
    }

    private function isPhpNowdoc(String_ $node): bool
    {
        if (String_::KIND_NOWDOC !== $node->getAttribute('kind')) {
            return false;
        }

        return 0 === strpos(
            substr(ltrim($node->value), 0, 5),
            '<?php'
        );
    }
}
