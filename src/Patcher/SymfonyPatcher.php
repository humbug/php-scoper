<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;


use function preg_replace;
use function strpos;

final class SymfonyPatcher
{
    private const PATHS = [
        'src/Symfony/Component/DependencyInjection/Dumper/PhpDumper.php',
        'symfony/dependency-injection/Dumper/PhpDumper.php',
    ];

    public function __invoke(string $filePath, string $prefix, string $contents): string {
        if (false === $this->isValidPath($filePath)) {
            return $contents;
        }

        return preg_replace(
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