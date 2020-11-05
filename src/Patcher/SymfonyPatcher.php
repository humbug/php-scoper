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

namespace Humbug\PhpScoper\Patcher;

use function preg_replace;
use function sprintf;
use function strpos;

final class SymfonyPatcher
{
    private const PATHS = [
        'src/Symfony/Component/DependencyInjection/Dumper/PhpDumper.php',
        'symfony/dependency-injection/Dumper/PhpDumper.php',
    ];

    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        if (false === $this->isValidPath($filePath)) {
            return $contents;
        }

        return (string) preg_replace(
            '/use (Symfony(\\\\(?:\\\\)?)Component\\\\.+?;)/',
            sprintf(
                'use %s$2$1',
                $prefix
            ),
            $contents
        );
    }

    private function isValidPath(string $filePath): bool
    {
        foreach (self::PATHS as $path) {
            if (false !== strpos($filePath, $path)) {
                return true;
            }
        }

        return false;
    }
}
