<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper\Composer;

/**
 * @internal
 */
final class AutoloadPrefixer
{
    /**
     * @param array  $content Decoded JSON
     * @param string $prefix
     *
     * @return array Prefixed decoded JSON
     */
    public static function prefixPackageAutoloads(array $content, string $prefix): array
    {
        if (isset($content['autoload'])) {
            $content['autoload'] = self::prefixAutoloads($content['autoload'], $prefix);
        }

        if (isset($content['autoload-dev'])) {
            $content['autoload-dev'] = self::prefixAutoloads($content['autoload-dev'], $prefix);
        }

        return $content;
    }

    private static function prefixAutoloads(array $autoload, string $prefix): array
    {
        if (isset($autoload['psr-4'])) {
            $autoload['psr-4'] = self::prefixAutoload($autoload['psr-4'], $prefix);
        }

        return $autoload;
    }

    private static function prefixAutoload(array $autoload, string $prefix): array
    {
        $loader = [];

        foreach ($autoload as $namespace => $paths) {
            $loader[sprintf('%s\\%s', $prefix, $namespace)] = $paths;
        }

        return $loader;
    }
}