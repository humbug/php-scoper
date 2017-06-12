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

namespace Humbug\PhpScoper\Console\Command;

use Humbug\PhpScoper\Handler\HandleAddPrefix;
use Humbug\PhpScoper\Logger\ConsoleLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

final class AddPrefixCommand extends Command
{
    /** @internal */
    const PATH_ARG = 'paths';
    /** @internal */
    const PREFIX_OPT = 'prefix';
    /** @internal */
    const OUTPUT_DIR_OPT = 'output-dir';
    /** @internal */
    const FORCE_OPT = 'force';

    private $fileSystem;
    private $handle;

    /**
     * @inheritdoc
     */
    public function __construct(Filesystem $fileSystem, HandleAddPrefix $handle)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;
        $this->handle = $handle;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('add-prefix')
            ->setDescription('Goes through all the PHP files found in the given paths to apply the given prefix to namespaces & FQNs.')
            ->addArgument(
                self::PATH_ARG,
                InputArgument::IS_ARRAY,
                'The path(s) to process.'
            )
            ->addOption(
                self::PREFIX_OPT,
                'p',
                InputOption::VALUE_REQUIRED,
                'The namespace prefix to add'
            )
            ->addOption(
                self::OUTPUT_DIR_OPT,
                'o',
                InputOption::VALUE_REQUIRED,
                'The output directory in which the prefixed code will be dumped.',
                'build'
            )
            ->addOption(
                self::FORCE_OPT,
                'f',
                InputOption::VALUE_NONE,
                'Deletes any existing content in the output directory without any warning'
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->validatePrefix($input);
        $this->validatePaths($input);
        $this->validateOutputDir($input, $io);

        $logger = new ConsoleLogger(
            $this->getApplication(),
            $io
        );

        $logger->outputScopingStart(
            $input->getOption(self::PREFIX_OPT),
            $input->getArgument(self::PATH_ARG)
        );

        try {
            $this->handle->__invoke(
                $input->getOption(self::PREFIX_OPT),
                $input->getArgument(self::PATH_ARG),
                $input->getOption(self::OUTPUT_DIR_OPT),
                $logger
            );
        } catch (Throwable $throwable) {
            $logger->outputScopingEndWithFailure();

            throw $throwable;
        }

        $logger->outputScopingEnd();
    }

    private function validatePrefix(InputInterface $input)
    {
        $prefix = $input->getOption(self::PREFIX_OPT);

        if (null === $prefix) {
            $prefix = uniqid('PhpScoper');
        } else {
            $prefix = trim($prefix);
        }

        if (1 === preg_match('/(?<prefix>.*?)\\\\*$/', $prefix, $matches)) {
            $prefix = $matches['prefix'];
        }

        if ('' === $prefix) {
            throw new RuntimeException(
                sprintf(
                    'Expected "%s" argument to be a non empty string.',
                    self::PREFIX_OPT
                )
            );
        }

        $input->setOption(self::PREFIX_OPT, $prefix);
    }

    private function validatePaths(InputInterface $input)
    {
        $cwd = getcwd();
        $fileSystem = $this->fileSystem;

        $paths = array_map(
            function (string $path) use ($cwd, $fileSystem) {
                if (false === $fileSystem->isAbsolutePath($path)) {
                    return $cwd.DIRECTORY_SEPARATOR.$path;
                }

                return $path;
            },
            $input->getArgument(self::PATH_ARG)
        );

        if (0 === count($paths)) {
            $paths[] = $cwd;
        }

        $input->setArgument(self::PATH_ARG, $paths);
    }

    private function validateOutputDir(InputInterface $input, OutputStyle $io)
    {
        $outputDir = $input->getOption(self::OUTPUT_DIR_OPT);

        if (false === $this->fileSystem->isAbsolutePath($outputDir)) {
            $outputDir = getcwd().DIRECTORY_SEPARATOR.$outputDir;
        }

        $input->setOption(self::OUTPUT_DIR_OPT, $outputDir);

        if (false === $this->fileSystem->exists($outputDir)) {
            return;
        }

        if (false === is_writable($outputDir)) {
            throw new RuntimeException(
                sprintf(
                    'Expected "<comment>%s</comment>" to be writeable.',
                    $outputDir
                )
            );
        }

        if ($input->getOption(self::FORCE_OPT)) {
            $this->fileSystem->remove($outputDir);

            return;
        }

        if (false === is_dir($outputDir)) {
            $canDeleteFile = $io->confirm(
                sprintf(
                    'Expected "<comment>%s</comment>" to be a directory but found a file instead. It will be '
                    .'removed, do you wish to proceed?',
                    $outputDir
                ),
                false
            );

            if (false === $canDeleteFile) {
                return;
            }

            $this->fileSystem->remove($outputDir);
        } else {
            $canDeleteFile = $io->confirm(
                sprintf(
                    'The output directory "<comment>%s</comment>" already exists. Continuing will erase its'
                    .' content, do you wish to proceed?',
                    $outputDir
                ),
                false
            );

            if (false === $canDeleteFile) {
                return;
            }

            $this->fileSystem->remove($outputDir);
        }
    }
}
