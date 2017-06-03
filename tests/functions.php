<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use Webmozart\PathUtil\Path;

/**
 * Creates a temporary directory.
 *
 * @param string $namespace The directory path in the system's temporary
 *                          directory.
 * @param string $className The name of the test class.
 *
 * @return string The path to the created directory.
 */
function makeTempDir(string $namespace, string $className): string
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

    while (false === @mkdir($tempDir = $basePath.rand(10000, 99999), 0777, true)) {
        // Run until we are able to create a directory
    }

    return $tempDir;
}