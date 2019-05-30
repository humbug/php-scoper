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

use Closure;
use LogicException;
use PhpParser\Parser;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use function sprintf;

/**
 * Creates a temporary directory.
 *
 * @param string $namespace The directory path in the system's temporary
 *                          directory.
 * @param string $className The name of the test class.
 *
 * @return string The path to the created directory.
 */
function make_tmp_dir(string $namespace, string $className): string
{
    if (false !== ($pos = strrpos($className, '\\'))) {
        $shortClass = substr($className, $pos + 1);
    } else {
        $shortClass = $className;
    }

    // Usage of realpath() is important if the temporary directory is a
    // symlink to another directory (e.g. /var => /private/var on some Macs)
    // We want to know the real path to avoid comparison failures with
    // code that uses real paths only
    $systemTempDir = str_replace('\\', '/', realpath(sys_get_temp_dir()));
    $basePath = $systemTempDir.'/'.$namespace.'/'.$shortClass;

    $i = 0;

    while (false === @mkdir($tempDir = escape_path($basePath.rand(10000, 99999)), 0777, true)) {
        // Run until we are able to create a directory
        if ($i > 100) {
            throw new RuntimeException(
                sprintf(
                    'Could not create temporary directory for "%s:%s" after 100 attempts',
                    $namespace,
                    $className
                )
            );
        }

        ++$i;
    }

    return $tempDir;
}

function escape_path(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function remove_dir(string $path): void
{
    $path = escape_path($path);

    (new Filesystem())->remove($path);
}

function create_fake_patcher(): Closure
{
    return static function (): void {
        throw new LogicException('Did not expect to be called');
    };
}

/**
 * @private
 */
function create_parser(): Parser
{
    return (new Container())->getParser();
}
