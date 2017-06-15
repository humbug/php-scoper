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

namespace Humbug\PhpScoper\Handler;

use Humbug\PhpScoper\Logger\ConsoleLogger;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use Humbug\PhpScoper\Throwable\Exception\RuntimeException;
use PhpParser\Error;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Throwable;
use function Humbug\PhpScoper\get_common_path;

/**
 * @final
 */
class HandleAddPrefix
{
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
     * @param string        $prefix e.g. 'Foo'
     * @param string[]      $paths  List of files to scan (absolute paths)
     * @param string        $output absolute path to the output directory
     * @param ConsoleLogger $logger
     */
    public function __invoke(string $prefix, array $paths, string $output, ConsoleLogger $logger)
    {
        $this->fileSystem->mkdir($output);

        try {
            $files = $this->retrieveFiles($paths, $output);

            $this->scopeFiles($files, $prefix, $logger);
        } catch (Throwable $throwable) {
            $this->fileSystem->remove($output);

            throw $throwable;
        }
    }

    /**
     * @param string[] $paths
     * @param string   $output
     *
     * @return string[]
     */
    private function retrieveFiles(array $paths, string $output): array
    {
        $pathsToSearch = [];
        $filesToAppend = [];

        foreach ($paths as $path) {
            if (false === file_exists($path)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not find the file "%s".',
                        $path
                    )
                );
            }

            if (is_dir($path)) {
                $pathsToSearch[] = $path;
            } else {
                $filesToAppend[] = $path;
            }
        }

        $finder = new Finder();

        $finder->files()
            ->in($pathsToSearch)
            ->append($filesToAppend)
            ->sortByName()
        ;

        $files = array_values(
            array_map(
                function (SplFileInfo $fileInfo) {
                    return $fileInfo->getRealPath();
                },
                iterator_to_array($finder)
            )
        );

        $commonPath = get_common_path($files);

        return array_reduce(
            $files,
            function (array $files, string $file) use ($output, $commonPath): array {
                if (false === file_exists($file)) {
                    throw new RuntimeException(
                        sprintf(
                            'Could not find the file "%s".',
                            $file
                        )
                    );
                }

                if (false === is_readable($file)) {
                    throw new RuntimeException(
                        sprintf(
                            'Could not read the file "%s".',
                            $file
                        )
                    );
                }

                $files[$file] = $output.str_replace($commonPath, '', $file);

                return $files;
            },
            []
        );
    }

    /**
     * @param string[]      $files
     * @param string        $prefix
     * @param ConsoleLogger $logger
     */
    private function scopeFiles(array $files, string $prefix, ConsoleLogger $logger)
    {
        $count = count($files);
        $logger->outputFileCount($count);

        foreach ($files as $inputFilePath => $outputFilePath) {
            $this->scopeFile($inputFilePath, $outputFilePath, $prefix, $logger);
        }
    }

    private function scopeFile(string $inputFilePath, string $outputFilePath, string $prefix, ConsoleLogger $logger)
    {
        try {
            $scoppedContent = $this->scoper->scope($inputFilePath, $prefix);
        } catch (Error $error) {
            throw new ParsingException(
                sprintf(
                    'Could not parse the file "%s".',
                    $inputFilePath
                ),
                0,
                $error
            );
        }

        $this->fileSystem->dumpFile($outputFilePath, $scoppedContent);

        $logger->outputSuccess($inputFilePath);
    }
}
