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
use Humbug\PhpScoper\Handler\HandleAddPrefix;
use Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper;
use Humbug\PhpScoper\Scoper\Composer\JsonFileScoper;
use Humbug\PhpScoper\Scoper\NullScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use PackageVersions\Versions;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Filesystem\Filesystem;

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
    ]);

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
    return new JsonFileScoper(
        new InstalledPackagesScoper(
            new PhpScoper(
                create_parser(),
                new NullScoper()
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
