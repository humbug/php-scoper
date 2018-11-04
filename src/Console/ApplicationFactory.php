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

namespace Humbug\PhpScoper\Console;

use Humbug\PhpScoper\Console\Command\AddPrefixCommand;
use Humbug\PhpScoper\Console\Command\InitCommand;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PatchScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\SymfonyScoper;
use PackageVersions\Versions;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Symfony\Component\Filesystem\Filesystem;

class ApplicationFactory
{
    public function create(): Application
    {
        $app = new Application('PHP Scoper', static::getVersion());

        $app->addCommands([
            new AddPrefixCommand(
                new Filesystem(),
                static::createScoper()
            ),
            new InitCommand(),
        ]);

        return $app;
    }

    protected static function getVersion(): string
    {
        if (0 === strpos(__FILE__, 'phar:')) {
            return '@git_version_placeholder@';
        }

        $rawVersion = Versions::getVersion('humbug/php-scoper');

        [$prettyVersion, $commitHash] = explode('@', $rawVersion);

        return (1 === preg_match('/9{7}/', $prettyVersion)) ? $commitHash : $prettyVersion;
    }

    protected static function createScoper(): Scoper
    {
        return new PatchScoper(
            new PhpScoper(
                static::createParser(),
                new JsonFileScoper(
                    new InstalledPackagesScoper(
                        new SymfonyScoper(
                            new NullScoper()
                        )
                    )
                ),
                new TraverserFactory(static::createReflector())
            )
        );
    }

    protected static function createParser(): Parser
    {
        return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    protected static function createReflector(): Reflector
    {
        $phpParser = static::createParser();
        $astLocator = new Locator($phpParser);

        $sourceLocator = new MemoizingSourceLocator(
            new PhpInternalSourceLocator($astLocator)
        );
        $classReflector = new ClassReflector($sourceLocator);

        return new Reflector($classReflector);
    }
}
