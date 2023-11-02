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

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;
use function array_splice;
use function count;
use function explode;
use function implode;
use function rtrim;
use function sprintf;
use function str_contains;
use function strlen;
use function substr;

final class InternalCommenter extends NodeVisitorAbstract
{
    public function enterNode(Node $node): Node
    {
        if ($node instanceof Class_
            || $node instanceof Const_
            || $node instanceof Enum_
            || $node instanceof Function_
            || $node instanceof Interface_
            || $node instanceof Trait_
        ) {
            self::addTagToPhpDoc($node);
        }

        return $node;
    }

    private static function addTagToPhpDoc(Class_|Const_|Enum_|Function_|Interface_|Trait_ $node): void
    {
        $node->setDocComment(
            new Doc(
                self::createText($node->getDocComment()),
            ),
        );
    }

    private static function createText(?Doc $existingDoc): string
    {
        if (null === $existingDoc) {
            return '/** @internal */';
        }

        $reformattedText = $existingDoc->getReformattedText();

        if (str_contains($reformattedText, '@internal')) {
            return $reformattedText;
        }

        $textLines = explode("\n", $reformattedText);

        if (count($textLines) > 1) {
            array_splice(
                $textLines,
                -1,
                0,
                [' * @internal'],
            );
        } else {
            $line = $textLines[0];
            $textLines = [
                sprintf(
                    "%s\n * @internal\n */",
                    rtrim(substr($line, 0, strlen($line) - 2)),
                ),
            ];
        }

        return implode("\n", $textLines);
    }
}
