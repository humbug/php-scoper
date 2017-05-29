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

use PhpParser\ParserFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PhpScoper\Exception\ParsingException;
use Webmozart\PhpScoper\Exception\RuntimeException;
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

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->scoper = new Scoper($parser);
    }

    /**
     * Handles the "add-prefix" command.
     *
     * @param Args $args The console arguments.
     * @param IO   $io   The I/O.
     *
     * @return int Returns 0 on success and a positive integer on error.
     */
    public function handle($prefix, array $paths, OutputInterface $output)
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

        if (0 == count($this->finder)) {
            $output->writeLn('No PHP files to scope located with given path(s).');
        } else {
            foreach ($this->finder as $file) {
                $this->scopeFile($file->getPathName(), $prefix, $output);
            }
        }

        return 0;
    }

    private function scopeFile($path, $prefix, OutputInterface $output)
    {
        $fileContent = file_get_contents($path);
        try {
            $scoppedContent = $this->scoper->addNamespacePrefix($fileContent, $prefix);
            $this->filesystem->dumpFile($path, $scoppedContent);
            $output->writeLn(sprintf('Scoping %s. . . Success', $path));
        } catch (ParsingException $exception) {
            $output->writeLn(sprintf('Scoping %s. . . Fail', $path));
        }
    }
}
