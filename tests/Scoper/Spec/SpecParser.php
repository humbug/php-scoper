<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper\Spec;

use Symfony\Component\Finder\SplFileInfo;
use Throwable;

final class SpecParser
{
    public function __construct(public readonly string $sourceDir)
    {
    }

    public function parse(SplFileInfo $specFile): iterable
    {
        try {
            $fixtures = include $file;

            $meta = $fixtures['meta'];
            unset($fixtures['meta']);

            foreach ($fixtures as $fixtureTitle => $fixtureSet) {
                yield from self::parseSpecFile(
                    basename($sourceDir).'/'.$file->getRelativePathname(),
                    $meta,
                    $fixtureTitle,
                    $fixtureSet,
                );
            }
        } catch (Throwable $throwable) {
            self::fail(
                sprintf(
                    'An error occurred while parsing the file "%s": %s',
                    $file,
                    $throwable->getMessage(),
                ),
            );
        }
    }
}