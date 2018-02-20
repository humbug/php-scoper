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

use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\Command\AddPrefixCommand;
use Humbug\PhpScoper\Console\Command\InitCommand;
use Humbug\PhpScoper\Console\Command\SelfUpdateCommand;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PatchScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\TraverserFactory;
use Humbug\SelfUpdate\Exception\RuntimeException as SelfUpdateRuntimeException;
use Humbug\SelfUpdate\Updater;
use Iterator;
use PackageVersions\Versions;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Filesystem\Filesystem;

// TODO: register this file to the list of functions if possible to be autoloaded

/**
 * @private
 */
function create_application(): SymfonyApplication
{
    $app = new Application('PHP Scoper', get_version());

    $app->addCommands([
        new AddPrefixCommand(
            new Filesystem(),
            create_scoper()
        ),
        new InitCommand(),
    ]);

    if ('phar:' === substr(__FILE__, 0, 5)) {
        try {
            $updater = new Updater();
        } catch (SelfUpdateRuntimeException $e) {
            /* Allow E2E testing of unsigned phar */
            $updater = new Updater(null, false);
        }
        $app->add(
            new SelfUpdateCommand(
                $updater
            )
        );
    }

    return $app;
}

/**
 * @private
 */
function get_version(): string
{
    $rawVersion = Versions::getVersion('humbug/php-scoper');

    list($prettyVersion, $commitHash) = explode('@', $rawVersion);

    return (1 === preg_match('/9{7}/', $prettyVersion)) ? $commitHash : $prettyVersion;
}

/**
 * @private
 */
function create_scoper(): Scoper
{
    return new PatchScoper(
        new PhpScoper(
            create_parser(),
            new JsonFileScoper(
                new InstalledPackagesScoper(
                    new NullScoper()
                )
            ),
            new TraverserFactory(create_reflector())
        )
    );
}

/**
 * @private
 */
function create_parser(): Parser
{
    return (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
}

function create_reflector(): Reflector
{
    $phpParser = create_parser();
    $astLocator = new Locator($phpParser);

    $sourceLocator = new MemoizingSourceLocator(
        new PhpInternalSourceLocator($astLocator)
    );
    $classReflector = new ClassReflector($sourceLocator);

    return new Reflector(
        $classReflector,
        new FunctionReflector($sourceLocator, $classReflector)
    );
}

/**
 * @param string[] $paths Absolute paths
 *
 * @return string
 */
function get_common_path(array $paths): string
{
    if (0 === count($paths)) {
        return '';
    }

    $lastOffset = 1;
    $common = DIRECTORY_SEPARATOR;

    while (false !== ($index = strpos($paths[0], DIRECTORY_SEPARATOR, $lastOffset))) {
        $dirLen = $index - $lastOffset + 1;
        $dir = substr($paths[0], $lastOffset, $dirLen);

        foreach ($paths as $path) {
            if (substr($path, $lastOffset, $dirLen) !== $dir) {
                if (0 < strlen($common) && DIRECTORY_SEPARATOR === $common[strlen($common) - 1]) {
                    $common = substr($common, 0, strlen($common) - 1);
                }

                return $common;
            }
        }

        $common .= $dir;
        $lastOffset = $index + 1;
    }

    $common = substr($common, 0, -1);

    if (0 < strlen($common) && DIRECTORY_SEPARATOR === $common[strlen($common) - 1]) {
        $common = substr($common, 0, strlen($common) - 1);
    }

    return $common;
}

/**
 * In-house clone functions. Does a partial clone that should be enough to provide the immutability required in some
 * places for the scoper. It however does not guarantee a deep cloning as would be horribly slow for no good reasons.
 * A better alternative would be to find a way to push immutability upstream in PHP-Parser directly.
 *
 * @param Node $node
 *
 * @return Node
 */
function clone_node(Node $node): Node
{
    $clone = deep_clone($node);

    foreach ($node->getAttributes() as $key => $attribute) {
        $clone->setAttribute($key, $attribute);
    }

    return $clone;
}

/**
 * @param mixed $node
 *
 * @return mixed
 *
 * @internal
 */
function deep_clone($node)
{
    if (is_array($node)) {
        return array_map(__FUNCTION__, $node);
    }

    if (null === $node || is_scalar($node)) {
        return $node;
    }

    return unserialize(serialize($node));
}

function chain(iterable ...$iterables): Iterator
{
    foreach ($iterables as $iterable) {
        foreach ($iterable as $key => $value) {
            yield $key => $value;
        }
    }
}
