<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use UnexpectedValueException;
use function count;
use function get_class;
use function Safe\sprintf;

final class UseStmtName
{
    private Name $name;

    public function __construct(Name $name)
    {
        $this->name = $name;
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
        $use = ParentNodeAppender::getParent($this->name);

        if (!($use instanceof UseUse)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unexpected use statement name parent "%s"',
                    get_class($use),
                ),
            );
        }

        $useParent = ParentNodeAppender::getParent($use);

        if (!($useParent instanceof Use_)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unexpected UseUse parent "%s"',
                    get_class($useParent),
                ),
            );
        }

        $alias = $use->alias;

        if (null !== $alias) {
            $alias = (string) $alias;
        }

        return [
            $alias,
            $useParent->type,
        ];
    }
}
