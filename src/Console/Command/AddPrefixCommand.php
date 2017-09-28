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

use Humbug\PhpScoper\Console\Configuration;
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
    private const PATH_ARG = 'paths';
    private const PREFIX_OPT = 'prefix';
    private const OUTPUT_DIR_OPT = 'output-dir';
    private const FORCE_OPT = 'force';
    private const STOP_ON_FAILURE_OPT = 'stop-on-failure';
    private const CONFIG_FILE_OPT = 'config';
    private const CONFIG_FILE_DEFAULT = 'scoper.inc.php';
    private const WORKING_DIR_OPT = 'working-dir';

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
    protected function configure(): void
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
                'The namespace prefix to add.'
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
                'Deletes any existing content in the output directory without any warning.'
            )
            ->addOption(
                self::STOP_ON_FAILURE_OPT,
                's',
                InputOption::VALUE_NONE,
                'Stops on failure.'
            )
            ->addOption(
                self::CONFIG_FILE_OPT,
                'c',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Configuration file. Will use "%s" if found by default',
                    self::CONFIG_FILE_DEFAULT
                ),
                null
            )
            ->addOption(
                self::WORKING_DIR_OPT,
                'd',
                InputOption::VALUE_REQUIRED,
                'If specified, use the given directory as working directory.',
                null
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $workingDir = $input->getOption(self::WORKING_DIR_OPT);

        if (null !== $workingDir) {
            chdir($workingDir);
        }

        $this->validatePrefix($input);
        $this->validatePaths($input);
        $this->validateOutputDir($input, $io);

        $config = $this->retrieveConfig($input, $io);

        $logger = new ConsoleLogger(
            $this->getApplication(),
            $io
        );

        $logger->outputScopingStart(
            $input->getOption(self::PREFIX_OPT),
            $input->getArgument(self::PATH_ARG)
        );

        $paths = $this->retrievePaths($input, $config);

        try {
            $this->handle->__invoke(
                $input->getOption(self::PREFIX_OPT),
                $paths,
                $input->getOption(self::OUTPUT_DIR_OPT),
                $config->getPatchers(),
                $config->getWhitelist(),
                $config->getGlobalNamespaceWhitelisters(),
                $input->getOption(self::STOP_ON_FAILURE_OPT),
                $logger
            );
        } catch (Throwable $throwable) {
            $logger->outputScopingEndWithFailure();

            throw $throwable;
        }

        $logger->outputScopingEnd();

        return 0;
    }

    private function validatePrefix(InputInterface $input): void
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

    private function validatePaths(InputInterface $input): void
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

        $input->setArgument(self::PATH_ARG, $paths);
    }

    private function validateOutputDir(InputInterface $input, OutputStyle $io): void
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

    private function retrieveConfig(InputInterface $input, OutputStyle $io): Configuration
    {
        $configFile = $input->getOption(self::CONFIG_FILE_OPT);

        if (null === $configFile) {
            $configFile = $this->makeAbsolutePath(self::CONFIG_FILE_DEFAULT);

            if (false === file_exists($configFile)) {
                $io->writeln(
                    sprintf(
                        'Config file "%s" not found. Skipping.',
                        $configFile
                    ),
                    OutputStyle::VERBOSITY_DEBUG
                );

                return Configuration::load(null);
            }
        } else {
            $configFile = $this->makeAbsolutePath($configFile);
        }

        if (false === file_exists($configFile)) {
            throw new RuntimeException(
                sprintf(
                    'Could not find the file "%s".',
                    $configFile
                )
            );
        }

        $io->writeln(
            sprintf(
                'Using the configuration file "%s".',
                $configFile
            ),
            OutputStyle::VERBOSITY_DEBUG
        );

        return Configuration::load($configFile);
    }

    /**
     * @param InputInterface $input
     * @param Configuration  $configuration
     *
     * @return string[] List of absolute paths
     */
    private function retrievePaths(InputInterface $input, Configuration $configuration): array
    {
        $paths = $input->getArgument(self::PATH_ARG);

        $finders = $configuration->getFinders();

        foreach ($finders as $finder) {
            foreach ($finder as $file) {
                $paths[] = $file->getRealPath();
            }
        }

        if (0 === count($paths)) {
            return [getcwd()];
        }

        return array_unique($paths);
    }

    private function makeAbsolutePath(string $path): string
    {
        if (false === $this->fileSystem->isAbsolutePath($path)) {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
        }

        return $path;
    }
}
