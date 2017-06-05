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

use Humbug\PhpScoper\Throwable\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use Humbug\PhpScoper\Logger\ConsoleLogger;
use Humbug\PhpScoper\Scoper;

/**
 * @final
 */
class HandleAddPrefix
{
    /** @internal */
    const PHP_FILE_PATTERN = '/\.php$/';

    private $fileSystem;
    private $scoper;

    public function __construct(Scoper $scoper)
    {
        $this->fileSystem = new Filesystem();
        $this->scoper = $scoper;
    }

    /**
     * Apply prefix to all the code found in the given paths, AKA scope all the files found.
     *
     * @param string        $prefix
     * @param string[]      $paths
     * @param ConsoleLogger $logger
     *
     * @throws RuntimeException
     */
    public function __invoke(string $prefix, array $paths, ConsoleLogger $logger)
    {
        $files = $this->retrieveFiles($paths);

        $this->scopeFiles($files, $prefix, $logger);
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
            } elseif (1 === preg_match(self::PHP_FILE_PATTERN, $path)) {
                $filesToAppend[] = $path;
            }
        }

        $finder = new Finder();

        $finder->files()
            ->name(self::PHP_FILE_PATTERN)
            ->in($pathsToSearch)
            ->append($filesToAppend)
            ->sortByName()
        ;

        return $finder;
    }

    private function scopeFiles(Finder $files, string $prefix, ConsoleLogger $logger)
    {
        $count = count($files);
        $logger->outputFileCount($count);

        foreach ($files as $file) {
            if (false === file_exists($file)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not find the path "%s".',
                        $file
                    )
                );
            }

            if (false === is_readable($file)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not read the path "%s".',
                        $file
                    )
                );
            }


            $this->scopeFile($file->getPathName(), $prefix, $logger);
        }
    }

    private function scopeFile(string $path, string $prefix, ConsoleLogger $logger)
    {
        $fileContent = file_get_contents($path);

        try {
            $scoppedContent = $this->scoper->scope($fileContent, $prefix);

            $this->fileSystem->dumpFile($path, $scoppedContent);

            $logger->outputSuccess($path);
        } catch (ParsingException $exception) {
            //TODO: display error in verbose mode
            $logger->outputFail($path);
        }
    }
}
