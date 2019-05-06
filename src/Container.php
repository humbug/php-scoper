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

use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PatchScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\SymfonyScoper;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\SourceStubber\AggregateSourceStubber;
use Roave\BetterReflection\SourceLocator\SourceStubber\PhpStormStubsSourceStubber;
use Roave\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use Roave\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;

final class Container
{
    private $parser;
    private $reflector;
    private $scoper;

    public function getScoper(): Scoper
    {
        if (null === $this->scoper) {
            $this->scoper = new PatchScoper(
                new PhpScoper(
                    $this->getParser(),
                    new JsonFileScoper(
                        new InstalledPackagesScoper(
                            new SymfonyScoper(
                                new NullScoper()
                            )
                        )
                    ),
                    new TraverserFactory($this->getReflector())
                )
            );
        }

        return $this->scoper;
    }

    public function getParser(): Parser
    {
        if (null === $this->parser) {
            $this->parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        }

        return $this->parser;
    }

    public function getReflector(): Reflector
    {
        if (null === $this->reflector) {
            $phpParser = $this->getParser();
            $astLocator = new Locator($phpParser);

            $sourceLocator = new MemoizingSourceLocator(
                new PhpInternalSourceLocator(
                    $astLocator,
                    new AggregateSourceStubber(
                        new PhpStormStubsSourceStubber($phpParser),
                        new ReflectionSourceStubber()
                    )
                )
            );

            $classReflector = new ClassReflector($sourceLocator);

            $functionReflector = new FunctionReflector($sourceLocator, $classReflector);

            return new Reflector($classReflector, $functionReflector);
        }

        return $this->reflector;
    }
}
