<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\Handler;

use PhpParser\ParserFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use Humbug\PhpScoper\Logger\ConsoleLogger;
use Humbug\PhpScoper\Scoper;

/**
 * @inheritdoc
 */
final class HandleAddPrefix
{
    private $fileSystem;
    private $scoper;

    public function __construct()
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        $this->fileSystem = new Filesystem();
        $this->scoper = new Scoper($parser);
    }

    /**
     * Apply prefix to all the code found in the given paths, AKA scope all the files found.
     *
     * @param string        $prefix
     * @param string[]      $paths
     * @param ConsoleLogger $formatter
     */
    public function __invoke(string $prefix, array $paths, ConsoleLogger $formatter)
    {
        $files = $this->retrieveFiles($paths);

        $this->scopeFiles($files, $prefix, $formatter);
    }

    /**
     * @param string[] $paths
     *
     * @return Finder
     */
    private function retrieveFiles(array $paths): Finder
    {
        $pathsToSearch = [];
        $filesToAppend = [];

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $pathsToSearch[] = $path;
            } else {
                $filesToAppend[] = $path;
            }
        }

        $finder = new Finder();

        $finder->files()
            ->name('*.php')
            ->in($pathsToSearch)
            ->append($filesToAppend)
            ->sortByName()
        ;

        return $finder;
    }

    private function scopeFiles(Finder $files, string $prefix, ConsoleLogger $formatter)
    {
        $count = count($files);
        $formatter->outputFileCount($count);

        foreach ($files as $file) {
            $this->scopeFile($file->getPathName(), $prefix, $formatter);
        }
    }

    private function scopeFile(string $path, string $prefix, ConsoleLogger $formatter)
    {
        $fileContent = file_get_contents($path);

        try {
            $scoppedContent = $this->scoper->addNamespacePrefix($fileContent, $prefix);

            $this->fileSystem->dumpFile($path, $scoppedContent);

            $formatter->outputSuccess($path);
        } catch (ParsingException $exception) {
            //TODO: display error in verbose mode
            $formatter->outputFail($path);
        }
    }
}
