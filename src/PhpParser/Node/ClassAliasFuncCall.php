<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\Node;


use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;

final class ClassAliasFuncCall extends FuncCall
{
    /**
     * @inheritdoc
     */
    public function __construct(FullyQualified $prefixedName, FullyQualified $originalName, array $attributes = [])
    {
        parent::__construct(
            new FullyQualified('class_alias'),
            [
                new Arg(
                    new String_((string) $prefixedName)
                ),
                new Arg(
                    new String_((string) $originalName)
                ),
                new Arg(
                    new ConstFetch(
                        new FullyQualified('false')
                    )
                ),
            ],
            $attributes
        );
    }
}