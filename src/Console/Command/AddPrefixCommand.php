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

use Fidry\Console\Application\Application;
use Fidry\Console\Command\Command;
use Fidry\Console\Command\CommandAware;
use Fidry\Console\Command\CommandAwareness;
use Fidry\Console\Command\Configuration as CommandConfiguration;
use Fidry\Console\ExitCode;
use Fidry\Console\IO;
use Humbug\PhpScoper\Configuration;
use Humbug\PhpScoper\ConfigurationFactory;
use Humbug\PhpScoper\Console\ConfigLoader;
use Humbug\PhpScoper\Console\ConsoleScoper;
use Humbug\PhpScoper\ScoperFactory;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use function array_map;
use function is_dir;
use function is_writable;
use function Safe\getcwd;
use function Safe\sprintf;
use const DIRECTORY_SEPARATOR;

final class AddPrefixCommand implements Command, CommandAware
{
    use CommandAwareness;

    private const PATH_ARG = 'paths';
    private const PREFIX_OPT = 'prefix';
    private const OUTPUT_DIR_OPT = 'output-dir';
    private const FORCE_OPT = 'force';
    private const STOP_ON_FAILURE_OPT = 'stop-on-failure';
    private const CONFIG_FILE_OPT = 'config';
    private const DEFAULT_CONFIG_FILE_PATH = 'scoper.inc.php';
    private const NO_CONFIG_OPT = 'no-config';

    private Filesystem $fileSystem;
    private ScoperFactory $scoperFactory;
    private bool $init = false;
    private Application $application;
    private ConfigurationFactory $configFactory;

    public function __construct(
        Filesystem $fileSystem,
        ScoperFactory $scoperFactory,
        Application $application,
        ConfigurationFactory $configFactory
    ) {
        $this->fileSystem = $fileSystem;
        $this->scoperFactory = $scoperFactory;
        $this->application = $application;
        $this->configFactory = $configFactory;
    }

    public function getConfiguration(): CommandConfiguration
    {
        return new CommandConfiguration(
            'add-prefix',
            'Goes through all the PHP files found in the given paths to apply the given prefix to namespaces & FQNs.',
            '',
            [
                new InputArgument(
                    self::PATH_ARG,
                    InputArgument::IS_ARRAY,
                    'The path(s) to process.'
                ),
            ],
            [
                ChangeableDirectory::createOption(),
                new InputOption(
                    self::PREFIX_OPT,
                    'p',
                    InputOption::VALUE_REQUIRED,
                    'The namespace prefix to add.',
                ),
                new InputOption(
                    self::OUTPUT_DIR_OPT,
                    'o',
                    InputOption::VALUE_REQUIRED,
                    'The output directory in which the prefixed code will be dumped.',
                    'build',
                ),
                new InputOption(
                    self::FORCE_OPT,
                    'f',
                    InputOption::VALUE_NONE,
                    'Deletes any existing content in the output directory without any warning.'
                ),
                new InputOption(
                    self::STOP_ON_FAILURE_OPT,
                    's',
                    InputOption::VALUE_NONE,
                    'Stops on failure.'
                ),
                new InputOption(
                    self::CONFIG_FILE_OPT,
                    'c',
                    InputOption::VALUE_REQUIRED,
                    sprintf(
                        'Conf,iguration file. Will use "%s" if found by default.',
                        self::DEFAULT_CONFIG_FILE_PATH
                    )
                ),
                new InputOption(
                    self::NO_CONFIG_OPT,
                    null,
                    InputOption::VALUE_NONE,
                    'Do not look for a configuration file.'
                ),
            ],
        );
    }

    public function execute(IO $io): int
    {
        $io->writeln('');

        ChangeableDirectory::changeWorkingDirectory($io);

        $paths = $this->getPathArguments($io);
        $outputDir = $this->getOutputDir($io);

        $config = $this->retrieveConfig($io, $paths);

        $this->getScoper()->scope(
            $io,
            $config,
            $paths,
            $outputDir,
            $io->getBooleanOption(self::STOP_ON_FAILURE_OPT),
        );

        return ExitCode::SUCCESS;
    }

    private function getOutputDir(IO $io): string
    {
        $outputDir = $io->getStringOption(self::OUTPUT_DIR_OPT);

        if (false === $this->fileSystem->isAbsolutePath($outputDir)) {
            $outputDir = getcwd().DIRECTORY_SEPARATOR.$outputDir;
        }

        if (false === $this->fileSystem->exists($outputDir)) {
            return $outputDir;
        }

        if (false === is_writable($outputDir)) {
            throw new RuntimeException(
                sprintf(
                    'Expected "<comment>%s</comment>" to be writeable.',
                    $outputDir
                )
            );
        }

        if ($io->getBooleanOption(self::FORCE_OPT)) {
            $this->fileSystem->remove($outputDir);

            return $outputDir;
        }

        $question = sprintf(
            is_dir($outputDir)
                ? 'The output directory "<comment>%s</comment>" already exists. Continuing will erase its content, do you wish to proceed?'
                : 'Expected "<comment>%s</comment>" to be a directory but found a file instead. It will be  removed, do you wish to proceed?',
            $outputDir,
        );

        $canDeleteFile = $io->confirm($question, false);

        if ($canDeleteFile) {
            $this->fileSystem->remove($outputDir);
        }

        return $outputDir;
    }

    /**
     * @param string[] $paths
     */
    private function retrieveConfig(IO $io, array $paths): Configuration
    {
        $configLoader = new ConfigLoader(
            $this->getCommandRegistry(),
            $this->fileSystem,
            $this->configFactory,
        );

        return $configLoader->loadConfig(
            $io,
            $io->getStringOption(self::PREFIX_OPT),
            $io->getBooleanOption(self::NO_CONFIG_OPT),
            $io->getNullableStringOption(self::CONFIG_FILE_OPT),
            self::DEFAULT_CONFIG_FILE_PATH,
            $this->init,
            $paths,
            getcwd(),
        );
    }

    /**
     * @return list<string> List of absolute paths
     */
    private function getPathArguments(IO $io): array
    {
        $cwd = getcwd();
        $fileSystem = $this->fileSystem;

        return array_map(
            static fn (string $path) => $fileSystem->isAbsolutePath($path)
                ? $path
                : $cwd.DIRECTORY_SEPARATOR.$path,
            $io->getStringArrayArgument(self::PATH_ARG),
        );
    }

    private function getScoper(): ConsoleScoper
    {
        return new ConsoleScoper(
            $this->fileSystem,
            $this->application,
            $this->scoperFactory,
        );
    }
}
