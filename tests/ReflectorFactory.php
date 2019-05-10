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

use PhpParser\ParserFactory;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Ast\Parser\MemoizingParser;
use Roave\BetterReflection\SourceLocator\SourceStubber\PhpStormStubsSourceStubber;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;

final class ReflectorFactory
{
    public static function create(string $code): Reflector
    {
        $astLocator = (new BetterReflection())->astLocator();

        $sourceLocator = new AggregateSourceLocator([
            new PhpInternalSourceLocator(
                $astLocator,
                new PhpStormStubsSourceStubber(
                    new MemoizingParser(
                        (new ParserFactory())->create(ParserFactory::ONLY_PHP7)
                    )
                )
            ),
            new StringSourceLocator($code, $astLocator),
        ]);

        $classReflector = new ClassReflector($sourceLocator);

        $functionReflector = new FunctionReflector($sourceLocator, $classReflector);

        return new Reflector($classReflector, $functionReflector);
    }
}
