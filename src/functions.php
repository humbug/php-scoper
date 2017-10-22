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
use Humbug\PhpScoper\Handler\HandleAddPrefix;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PatchScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\TraverserFactory;
use Humbug\SelfUpdate\Exception\RuntimeException as SelfUpdateRuntimeException;
use Humbug\SelfUpdate\Updater;
use PackageVersions\Versions;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Filesystem\Filesystem;
use UnexpectedValueException;

/**
 * @private
 */
function create_application(): SymfonyApplication
{
    $app = new Application('PHP Scoper', get_version());

    $app->addCommands([
        new AddPrefixCommand(
            new Filesystem(),
            new HandleAddPrefix(
                create_scoper()
            )
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
    if ('phar:' === substr(__FILE__, 0, 5)) {
        $gitVersion = '@git-version@';
        $semanticVersion = preg_replace(
            ["/\.\d\-/", "/\-/"],
            ['-', '-dev+'],
            $gitVersion,
            1
        );

        return $semanticVersion;
    }

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
        new JsonFileScoper(
            new InstalledPackagesScoper(
                new PhpScoper(
                    create_parser(),
                    new NullScoper(),
                    new TraverserFactory()
                )
            )
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

    $sort_by_strlen = create_function('$a, $b', 'if (strlen($a) == strlen($b)) { return strcmp($a, $b); } return (strlen($a) < strlen($b)) ? -1 : 1;');
    usort($paths, $sort_by_strlen);
    
    $longest_common_substring = array();
    $shortest_string = str_split(array_shift($paths));

    while (sizeof($shortest_string)) {
        array_unshift($longest_common_substring, '');

        foreach ($shortest_string as $ci => $char) {

            foreach ($paths as $wi => $word) {
                if (!strstr($word, $longest_common_substring[0] . $char)) {
                    break 2;
                }
            }

            $longest_common_substring[0].= $char;
        }

        array_shift($shortest_string);
    }

    usort($longest_common_substring, $sort_by_strlen);

    return rtrim(array_pop($longest_common_substring), DIRECTORY_SEPARATOR);
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

    if ($node instanceof Node\Stmt\Namespace_) {
        return new Node\Stmt\Namespace_(
            deep_clone($node->name)
        );
    }

    if ($node instanceof Node\Name\Relative) {
        return new Node\Name\Relative(
            deep_clone($node->toString())
        );
    }

    if ($node instanceof Node\Name\FullyQualified) {
        return new Node\Name\FullyQualified(
            deep_clone($node->toString())
        );
    }

    if ($node instanceof Node\Name) {
        return new Node\Name(
            deep_clone($node->toString())
        );
    }

    if ($node instanceof Node\Stmt\Use_) {
        return new Node\Stmt\Use_(
            deep_clone($node->uses),
            $node->type
        );
    }

    if ($node instanceof Node\Stmt\UseUse) {
        return new Node\Stmt\UseUse(
            deep_clone($node->name),
            $node->alias,
            $node->type
        );
    }

    if ($node instanceof Node\Stmt\Class_) {
        return new Node\Stmt\Class_(
            deep_clone($node->name),
            [
                'flags' => deep_clone($node->flags),
                'extends' => deep_clone($node->extends),
                'implements' => deep_clone($node->implements),
            ]
        );
    }

    throw new UnexpectedValueException(
        sprintf(
            'Cannot clone element "%s".',
            get_class($node)
        )
    );
}
