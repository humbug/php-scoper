<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Handler;

use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PhpScoper\Exception\ParsingException;
use Webmozart\PhpScoper\Exception\RuntimeException;
use Webmozart\PhpScoper\Formatter\BasicFormatter;
use Webmozart\PhpScoper\Scoper;

/**
 * Handles the "add-prefix" command.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AddPrefixCommandHandler
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var Scoper
     */
    private $scoper;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Lexer([
            'usedAttributes' => [
                'startFilePos',
            ],
        ]));
        $this->scoper = new Scoper($parser);
    }

    /**
     * Handles the "add-prefix" command.
     *
     * @param string         $prefix
     * @param string[]       $paths
     * @param BasicFormatter $formatter
     *
     * @return int Returns 0 on success and a positive integer on error.
     */
    public function handle($prefix, array $paths, BasicFormatter $formatter)
    {
        $prefix = rtrim($prefix, '\\');
        $pathsToSearch = [];
        $filesToAppend = [];

        foreach ($paths as $path) {
            if (!$this->filesystem->isAbsolutePath($path)) {
                $path = getcwd().DIRECTORY_SEPARATOR.$path;
            }

            if (($exists = !file_exists($path)) || !is_readable($path)) {
                $issue = $exists ? 'does not exist' : 'is not readable';
                throw new RuntimeException(sprintf(
                    'A given path %s: %s',
                    $issue,
                    $path
                ));
            }

            if (is_dir($path)) {
                $pathsToSearch[] = $path;
            } else {
                $filesToAppend[] = $path;
            }
        }

        $this->finder->files()
            ->name('*.php')
            ->in($pathsToSearch)
            ->append($filesToAppend)
            ->sortByName();

        $this->scopeFiles($prefix, $formatter);

        return 0;
    }

    /**
     * Scopes all files attached to Finder instance.
     *
     * @param string         $prefix
     * @param BasicFormatter $formatter
     */
    private function scopeFiles($prefix, BasicFormatter $formatter)
    {
        $count = count($this->finder);
        $formatter->outputFileCount($count);

        foreach ($this->finder as $file) {
            $this->scopeFile($file->getPathName(), $prefix, $formatter);
        }
    }

    /**
     * Scopes a given file.
     *
     * @param string         $path
     * @param string         $prefix
     * @param BasicFormatter $formatter
     */
    private function scopeFile($path, $prefix, BasicFormatter $formatter)
    {
        $fileContent = file_get_contents($path);
        try {
            $scoppedContent = $this->scoper->addNamespacePrefix($fileContent, $prefix);
            $this->filesystem->dumpFile($path, $scoppedContent);
            $formatter->outputSuccess($path);
        } catch (ParsingException $exception) {
            $formatter->outputFail($path);
        }
    }
}
