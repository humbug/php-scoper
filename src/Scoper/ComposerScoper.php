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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Scoper;

final class ComposerScoper implements Scoper
{
    private $decoratedScoper;

    public function __construct(Scoper $decoratedScoper)
    {
        $this->decoratedScoper = $decoratedScoper;
    }

    /**
     * Scopes PHP and JSON files related to Composer.
     *
     * {@inheritdoc}
     */
    public function scope(string $filePath, string $prefix): string
    {
        if (preg_match('/composer\.lock$/', $filePath)) {
            return file_get_contents($filePath);
        }

        if (1 !== preg_match('/composer\.json$/', $filePath)) {
            return $this->decoratedScoper->scope($filePath, $prefix);
        }

        $decodedJson = json_decode(
            file_get_contents($filePath),
            true
        );

        if (isset($decodedJson['autoload'])) {
            $decodedJson['autoload'] = $this->prefixAutoloaders($decodedJson['autoload'], $prefix);
        }

        if (isset($decodedJson['autoload-dev'])) {
            $decodedJson['autoload-dev'] = $this->prefixAutoloaders($decodedJson['autoload-dev'], $prefix);
        }

        return json_encode(
            $decodedJson,
            JSON_PRETTY_PRINT
        );
    }

    private function prefixAutoloaders(array $autoloader, string $prefix): array
    {
        if (isset($autoloader['psr-4'])) {
            $autoloader['psr-4'] = $this->prefixAutoloader($autoloader['psr-4'], $prefix);
        }

        return $autoloader;
    }

    private function prefixAutoloader(array $autoloader, string $prefix): array
    {
        $loader = [];

        foreach ($autoloader as $namespace => $paths) {
            $loader[sprintf('%s\\%s', $prefix, $namespace)] = $paths;
        }

        return $loader;
    }
}
