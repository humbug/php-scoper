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

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $scoper = new Scoper($parser);

        foreach ($paths as $path) {
            if (!$this->filesystem->isAbsolutePath($path)) {
                $path = getcwd().DIRECTORY_SEPARATOR.$path;
            }

            if (!is_file($path)) {
                continue;
            }

            $fileContent = file_get_contents($path);
            $scoppedContent = $scoper->scope($fileContent, $prefix);
            $this->filesystem->dumpFile($path, $scoppedContent);
        }

        $io->writeLine('...');

        return 0;
    }
}
