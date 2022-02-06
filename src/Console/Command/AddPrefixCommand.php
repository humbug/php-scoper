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
use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Console\ConfigLoader;
use Humbug\PhpScoper\Console\ConsoleScoper;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use function array_map;
use function is_dir;
use function is_writable;
use function Safe\getcwd;
use function Safe\sprintf;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class AddPrefixCommand implements Command, CommandAware
{
    use CommandAwareness;

    private const PATH_ARG = 'paths';
    private const PREFIX_OPT = 'prefix';
    private const OUTPUT_DIR_OPT = 'output-dir';
    private const FORCE_OPT = 'force';
    private const STOP_ON_FAILURE_OPT = 'stop-on-failure';
    private const CONFIG_FILE_OPT = 'config';
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
                        ConfigurationFactory::DEFAULT_FILE_NAME,
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
        $io->newLine();

        ChangeableDirectory::changeWorkingDirectory($io);

        // Only get current working directory _after_ we changed to the desired
        // working directory
        $cwd = getcwd();

        $paths = $this->getPathArguments($io, $cwd);
        $outputDir = $this->getOutputDir($io, $cwd);

        $this->checkOutputDir($io, $outputDir);

        $config = $this->retrieveConfig($io, $paths, $cwd);

        $this->getScoper()->scope(
            $io,
            $config,
            $paths,
            $outputDir,
            $io->getBooleanOption(self::STOP_ON_FAILURE_OPT),
        );

        return ExitCode::SUCCESS;
    }

    private function getOutputDir(IO $io, string $cwd): string
    {
        return $this->canonicalizePath(
            $io->getStringOption(self::OUTPUT_DIR_OPT),
            $cwd,
        );
    }

    private function checkOutputDir(IO $io, string $outputDir): void
    {
        if (!$this->fileSystem->exists($outputDir)) {
            return;
        }

        self::checkPathIsWriteable($outputDir);

        $canDeleteFile = self::canDeleteOutputDir($io, $outputDir);

        if (!$canDeleteFile) {
            throw new RuntimeException('Cannot delete the output directory. Interrupting the process.');
        }

        $this->fileSystem->remove($outputDir);
    }

    private static function checkPathIsWriteable(string $path): void
    {
        if (!is_writable($path)) {
            throw new RuntimeException(
                sprintf(
                    'Expected "<comment>%s</comment>" to be writeable.',
                    $path,
                ),
            );
        }
    }

    private static function canDeleteOutputDir(IO $io, string $outputDir): bool
    {
        if ($io->getBooleanOption(self::FORCE_OPT)) {
            return true;
        }

        $question = sprintf(
            is_dir($outputDir)
                ? 'The output directory "<comment>%s</comment>" already exists. Continuing will erase its content, do you wish to proceed?'
                : 'Expected "<comment>%s</comment>" to be a directory but found a file instead. It will be  removed, do you wish to proceed?',
            $outputDir,
        );

        return $io->confirm($question, false);
    }

    /**
     * @param list<non-empty-string> $paths
     */
    private function retrieveConfig(IO $io, array $paths, string $cwd): Configuration
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
            $this->getConfigFilePath($io, $cwd),
            ConfigurationFactory::DEFAULT_FILE_NAME,
            $this->init,
            $paths,
            $cwd,
        );
    }

    /**
     * @return non-empty-string|null
     */
    private function getConfigFilePath(IO $io, string $cwd): ?string
    {
        $configFilePath = $io->getStringOption(self::CONFIG_FILE_OPT);

        return '' === $configFilePath ? null : $this->canonicalizePath($configFilePath, $cwd);
    }

    /**
     * @return list<non-empty-string> List of absolute canonical paths
     */
    private function getPathArguments(IO $io, string $cwd): array
    {
        return array_map(
            fn (string $path) => $this->canonicalizePath($path, $cwd),
            $io->getStringArrayArgument(self::PATH_ARG),
        );
    }

    /**
     * @return non-empty-string Absolute canonical path
     */
    private function canonicalizePath(string $path, string $cwd): string
    {
        return Path::canonicalize(
            $this->fileSystem->isAbsolutePath($path)
                ? $path
                : $cwd.DIRECTORY_SEPARATOR.$path,
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
