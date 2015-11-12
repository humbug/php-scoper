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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\IO\IO;
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
    public function handle(Args $args, IO $io)
    {
        $prefix = rtrim($args->getArgument('prefix'), '\\');
        $paths = $args->getArgument('path');

        foreach ($paths as $path) {
            if (!$this->filesystem->isAbsolutePath($path)) {
                $path = getcwd().DIRECTORY_SEPARATOR.$path;
            }

            if (is_dir($path)) {
                $this->finder->files()->name('*.php')->in($path);

                foreach ($this->finder as $file) {
                    $this->scopeFile($file->getPathName(), $prefix, $io);
                }
            }

            if (!is_file($path)) {
                continue;
            }

            $this->scopeFile($path, $prefix, $io);
        }

        return 0;
    }

    private function scopeFile($path, $prefix, IO $io)
    {
        $io->write(sprintf('Scoping %s. . . ', $path));

        $fileContent = file_get_contents($path);
        $scoppedContent = $this->scoper->addNamespacePrefix($fileContent, $prefix);
        $this->filesystem->dumpFile($path, $scoppedContent);

        $io->writeLine('Success');
    }
}
