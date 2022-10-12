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

use Humbug\PhpScoper\PhpParser\StringNodePrefixer;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use function ltrim;
use function Safe\substr;
use function strpos;

final class NewdocPrefixer extends NodeVisitorAbstract
{
    private StringNodePrefixer $stringPrefixer;

    public function __construct(StringNodePrefixer $stringPrefixer)
    {
        $this->stringPrefixer = $stringPrefixer;
    }

    public function enterNode(Node $node): Node
    {
        if ($node instanceof String_ && $this->isPhpNowdoc($node)) {
            $this->stringPrefixer->prefixStringValue($node);
        }

        return $node;
    }

    private function isPhpNowdoc(String_ $node): bool
    {
        if (String_::KIND_NOWDOC !== $node->getAttribute('kind')) {
            return false;
        }

        return 0 === strpos(
            substr(
                ltrim($node->value),
                0,
                5,
            ),
            '<?php',
        );
    }
}
