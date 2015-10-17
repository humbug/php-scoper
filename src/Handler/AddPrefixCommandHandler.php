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
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\IO\IO;

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
     * @var Parser
     */
    private $parser;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
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

            if (!is_file($path)) {
                continue;
            }

            $content = file_get_contents($path);

            //TODO Manage errors
            $statements = $this->parser->parse($content);

            foreach ($statements as $statement) {
                if ($statement instanceof Namespace_) {
                    if ($statement->name->parts[0] !== $prefix) {
                        $statement->name = Name::concat($prefix, $statement->name);
                    }
                }
            }

            $prettyPrinter = new Standard();

            file_put_contents($path, $prettyPrinter->prettyPrintFile($statements)."\n");
        }

        $io->writeLine('...');

        // search all $paths, add $prefix to all namespace declarations, use
        // statements and class usages with fully-qualified class names

        return 0;
    }
}
