<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

class Scoper
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param $content
     * @param $prefix
     *
     * @return string
     */
    public function scope($content, $prefix)
    {
        //TODO Manage errors
        $statements = $this->parser->parse($content);

        foreach ($statements as $statement) {
            if ($statement instanceof Namespace_) {
                if ($statement->name->parts[0] !== $prefix) {
                    $statement->name = Name::concat($prefix, $statement->name);
                }
            }
        }

        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($statements)."\n";
    }
}
