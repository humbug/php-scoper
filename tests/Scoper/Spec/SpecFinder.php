<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper\Spec;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class SpecFinder
{
    private const SPECS_PATH = __DIR__.'/../../../specs';
    private const SECONDARY_SPECS_PATH = __DIR__.'/../../../_specs';

    /**
     * @return array{string, iterable<SplFileInfo>}
     */
    public static function findSpecFiles(): array
    {
        $sourceDir = self::SECONDARY_SPECS_PATH;
        $files = self::findFiles($sourceDir);

        if (0 === count($files)) {
            $sourceDir = self::SPECS_PATH;
            $files = self::findFiles($sourceDir);
        }

        $files->sortByName();

        return [$sourceDir, $files];
    }

    private static function findFiles(string $sourceDir): Finder
    {
        return (new Finder())->files()->in($sourceDir);
    }
}