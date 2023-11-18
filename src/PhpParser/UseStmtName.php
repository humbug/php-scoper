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

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender\ParentNodeAppender;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use function count;
use function sprintf;

final readonly class UseStmtName
{
    public function __construct(private Name $name)
    {
    }

    public function contains(Name $resolvedName): bool
    {
        return self::arrayStartsWith(
            $resolvedName->parts,
            $this->name->parts,
        );
    }

    /**
     * @param string[] $array
     * @param string[] $start
     */
    private static function arrayStartsWith(array $array, array $start): bool
    {
        $prefixLength = count($start);

        for ($index = 0; $index < $prefixLength; ++$index) {
            if (!isset($array[$index]) || $array[$index] !== $start[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{string|null, Use_::TYPE_*}
     */
    public function getUseStmtAliasAndType(): array
    {
        $use = self::getUseNode($this->name);
        $useParent = self::getUseParentNode($use);

        $alias = $use->alias;

        if (null !== $alias) {
            $alias = (string) $alias;
        }

        return [
            $alias,
            $useParent->type,
        ];
    }

    private static function getUseNode(Name $name): UseUse
    {
        $use = ParentNodeAppender::getParent($name);

        if ($use instanceof UseUse) {
            return $use;
        }

        // @codeCoverageIgnoreStart
        throw new UnexpectedParsingScenario(
            sprintf(
                'Unexpected use statement name parent "%s"',
                $use::class,
            ),
        );
        // @codeCoverageIgnoreEnd
    }

    private static function getUseParentNode(UseUse $use): Use_
    {
        $useParent = ParentNodeAppender::getParent($use);

        if ($useParent instanceof Use_) {
            return $useParent;
        }

        // @codeCoverageIgnoreStart
        throw new UnexpectedParsingScenario(
            sprintf(
                'Unexpected UseUse parent "%s"',
                $useParent::class,
            ),
        );
        // @codeCoverageIgnoreEnd
    }
}
