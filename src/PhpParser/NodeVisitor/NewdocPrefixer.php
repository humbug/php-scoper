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

use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Whitelist;
use function ltrim;
use PhpParser\Error as PhpParserError;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use function strpos;
use function substr;

final class NewdocPrefixer extends NodeVisitorAbstract
{
    private $scoper;
    private $prefix;
    private $whitelist;

    public function __construct(PhpScoper $scoper, string $prefix, Whitelist $whitelist)
    {
        $this->scoper = $scoper;
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof String_ && $this->isPhpNowdoc($node)) {
            try {
                $lastChar = substr($node->value, -1);

                $newValue = $this->scoper->scopePhp($node->value, $this->prefix, $this->whitelist);

                if ("\n" !== $lastChar) {
                    $newValue = substr($newValue, 0, -1);
                }

                $node->value = $newValue;
            } catch (PhpParserError $error) {
                // Continue without scoping the heredoc which for some reasons contains invalid PHP code
            }
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
