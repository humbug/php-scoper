<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Autoload;

use Symfony\Component\Filesystem\Path;
use function array_map;
use function md5;
use function preg_match;
use function sprintf;

final class FileHashGenerator
{
    private const ROOT_PACKAGE_NAME = '__root__';
    private const PACKAGE_PATH_REGEX = '~^%s/(?<vendor>[^/]+?/[^/]+?)/(?<path>[^/]+?)$~';

    private string $vendorDirRelativeToRoot;
    private string $packagePathRegex;

    public function __construct(
        private string $vendorDir,
        private string $rootDir,
        private array $filePaths,
    ) {
        $this->vendorDirRelativeToRoot = Path::makeRelative($this->vendorDir, $this->rootDir);
        $this->packagePathRegex = sprintf(
            self::PACKAGE_PATH_REGEX,
            $this->vendorDirRelativeToRoot,
        );
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

    private function generateHash(string $filePath): ?string
    {
        $relativePath = Path::makeRelative($filePath, $this->rootDir);

        if (1 === preg_match($this->packagePathRegex, $relativePath, $matches)) {
            $vendor = $matches['vendor'];
            $path = $matches['path'];
        } else {
            $vendor = self::ROOT_PACKAGE_NAME;
            $path = $relativePath;
        }

        $x = md5($vendor.':'.$path);

        return md5($vendor.':'.$path);
    }
}