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

namespace Humbug\PhpScoper\Autoload;

use Symfony\Component\Filesystem\Path;
use function array_map;
use function md5;
use function preg_match;
use function sprintf;

final readonly class ComposerFileHasher
{
    private const ROOT_PACKAGE_NAME = '__root__';
    private const PACKAGE_PATH_REGEX = '~^%s/(?<vendor>[^/]+?/[^/]+?)/(?<path>.+?)$~';

    /**
     * @param string[] $filePaths
     */
    public static function create(
        string $vendorDir,
        string $rootDir,
        array $filePaths,
    ): self {
        $vendorDirRelativeToRoot = Path::makeRelative($vendorDir, $rootDir);

        $packagePathRegex = sprintf(
            self::PACKAGE_PATH_REGEX,
            $vendorDirRelativeToRoot,
        );

        return new self(
            $rootDir,
            $filePaths,
            $packagePathRegex,
        );
    }

    /**
     * @param string[] $filePaths
     */
    public function __construct(
        private string $rootDir,
        private array $filePaths,
        private string $packagePathRegex,
    ) {
    }

    /**
     * @return string[]
     */
    public function generateHashes(): array
    {
        return array_map(
            $this->generateHash(...),
            $this->filePaths,
        );
    }

    /**
     * @see \Composer\Autoload::getFileIdentifier()
     */
    private function generateHash(string $filePath): string
    {
        $relativePath = Path::makeRelative($filePath, $this->rootDir);

        if (1 === preg_match($this->packagePathRegex, $relativePath, $matches)) {
            $vendor = $matches['vendor'];
            $path = $matches['path'];
        } else {
            $vendor = self::ROOT_PACKAGE_NAME;
            $path = $relativePath;
        }

        return md5($vendor.':'.$path);
    }
}
