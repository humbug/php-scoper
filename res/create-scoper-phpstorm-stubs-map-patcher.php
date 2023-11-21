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

// should be kept intact except for its namespace, which needs to be prefixed, as otherwise,
// its autoloading would break.

return static function (
    ?string $stubsMapPath = null,
    ?string $stubsMapVendorPath = null,
): Closure {
    $stubsMapVendorPath ??= 'vendor/jetbrains/phpstorm-stubs/PhpStormStubsMap.php';
    $stubsMapPath ??= __DIR__.'/../'.$stubsMapVendorPath;

    $stubsMapOriginalContent = file_get_contents($stubsMapPath);

    if (!preg_match('/class PhpStormStubsMap([\s\S]+)/', $stubsMapOriginalContent, $matches)) {
        throw new InvalidArgumentException('Could not capture the map original content.');
    }

    $stubsMapClassOriginalContent = $matches[1];

    return static function (string $filePath, string $prefix, string $contents) use (
        $stubsMapVendorPath,
        $stubsMapClassOriginalContent,
    ): string {
        if ($filePath !== $stubsMapVendorPath) {
            return $contents;
        }

        return preg_replace(
            '/class PhpStormStubsMap([\s\S]+)/',
            'class PhpStormStubsMap'.$stubsMapClassOriginalContent,
            $contents,
        );
    };
};
