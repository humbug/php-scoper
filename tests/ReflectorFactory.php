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

namespace Humbug\PhpScoper;

use PhpParser\Parser;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\ConstantReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Ast\Parser\MemoizingParser;
use Roave\BetterReflection\SourceLocator\SourceStubber\PhpStormStubsSourceStubber;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;

final class ReflectorFactory
{
    public static function create(?string $code, Parser $parser = null): Reflector
    {
        $astLocator = (new BetterReflection())->astLocator();

        $locators = [
            new PhpInternalSourceLocator(
                $astLocator,
                new PhpStormStubsSourceStubber(
                    new MemoizingParser(
                        $parser ?? create_parser()
                    )
                )
            ),
        ];

        if (null !== $code) {
            $locators[] = new StringSourceLocator($code, $astLocator);
        }

        $sourceLocator = new AggregateSourceLocator($locators);

        $classReflector = new ClassReflector($sourceLocator);

        return new Reflector(
            $classReflector,
            new FunctionReflector($sourceLocator, $classReflector),
            new ConstantReflector($sourceLocator, $classReflector)
        );
    }
}
