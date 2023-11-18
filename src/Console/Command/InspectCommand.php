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

use Fidry\Console\Command\Command;
use Fidry\Console\Command\CommandAware;
use Fidry\Console\Command\CommandAwareness;
use Fidry\Console\Command\Configuration as CommandConfiguration;
use Fidry\Console\ExitCode;
use Fidry\Console\IO;
use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Console\ConfigLoader;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use function array_key_exists;
use function Safe\getcwd;
use function sprintf;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class InspectCommand implements Command, CommandAware
{
    use CommandAwareness;

    private const FILE_PATH_ARG = 'file-path';
    private const PREFIX_OPT = 'prefix';
    private const CONFIG_FILE_OPT = 'config';
    private const NO_CONFIG_OPT = 'no-config';

    public function __construct(
        private readonly Filesystem $fileSystem,
        private readonly ScoperFactory $scoperFactory,
        private readonly ConfigurationFactory $configFactory,
    ) {
    }

    public function getConfiguration(): CommandConfiguration
    {
        return new CommandConfiguration(
            'inspect',
            'Outputs the processed file content based on the configuration.',
            '',
            [
                new InputArgument(
                    self::FILE_PATH_ARG,
                    InputArgument::REQUIRED,
                    'The file path to process.',
                ),
            ],
            [
                ChangeableDirectory::createOption(),
                new InputOption(
                    self::PREFIX_OPT,
                    'p',
                    InputOption::VALUE_REQUIRED,
                    'The namespace prefix to add.',
                    '',
                ),
                new InputOption(
                    self::CONFIG_FILE_OPT,
                    'c',
                    InputOption::VALUE_REQUIRED,
                    sprintf(
                        'Configuration file. Will use "%s" if found by default.',
                        ConfigurationFactory::DEFAULT_FILE_NAME,
                    ),
                ),
                new InputOption(
                    self::NO_CONFIG_OPT,
                    null,
                    InputOption::VALUE_NONE,
                    'Do not look for a configuration file.',
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

        $filePath = $this->getFilePath($io, $cwd);
        $config = $this->retrieveConfig($io, [$filePath], $cwd);

        if (array_key_exists($filePath, $config->getExcludedFilesWithContents())) {
            $io->writeln('The file was ignored as part of the excluded files.');

            return ExitCode::SUCCESS;
        }

        $symbolsRegistry = new SymbolsRegistry();
        $fileContents = $config->getFilesWithContents()[$filePath][1];

        $scoppedContents = $this->scopeFile($config, $symbolsRegistry, $filePath, $fileContents);

        $this->printScoppedContents($io, $scoppedContents, $symbolsRegistry);

        return ExitCode::SUCCESS;
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
            $io->getTypedOption(self::PREFIX_OPT)->asString(),
            $io->getTypedOption(self::NO_CONFIG_OPT)->asBoolean(),
            $this->getConfigFilePath($io, $cwd),
            ConfigurationFactory::DEFAULT_FILE_NAME,
            false,
            $paths,
            $cwd,
        );
    }

    /**
     * @return non-empty-string|null
     */
    private function getConfigFilePath(IO $io, string $cwd): ?string
    {
        $configFilePath = (string) $io->getTypedOption(self::CONFIG_FILE_OPT)->asNullableString();

        return '' === $configFilePath ? null : $this->canonicalizePath($configFilePath, $cwd);
    }

    /**
     * @return non-empty-string
     */
    private function getFilePath(IO $io, string $cwd): string
    {
        return $this->canonicalizePath(
            $io->getTypedArgument(self::FILE_PATH_ARG)->asNonEmptyString(),
            $cwd,
        );
    }

    /**
     * @return non-empty-string Absolute canonical path
     */
    private function canonicalizePath(string $path, string $cwd): string
    {
        $canonicalPath = Path::canonicalize(
            $this->fileSystem->isAbsolutePath($path)
                ? $path
                : $cwd.DIRECTORY_SEPARATOR.$path,
        );

        if ('' === $canonicalPath) {
            throw new InvalidArgumentException('Cannot canonicalize empty path and empty working directory');
        }

        return $canonicalPath;
    }

    private function scopeFile(
        Configuration $config,
        SymbolsRegistry $symbolsRegistry,
        string $filePath,
        string $fileContents,
    ): string {
        $scoper = $this->scoperFactory->createScoper(
            $config,
            $symbolsRegistry,
        );

        return $scoper->scope(
            $filePath,
            $fileContents,
        );
    }

    private function printScoppedContents(
        IO $io,
        string $scoppedContents,
        SymbolsRegistry $symbolsRegistry,
    ): void {
        if ($io->isQuiet()) {
            $io->writeln($scoppedContents, OutputInterface::VERBOSITY_QUIET);
        } else {
            $io->writeln([
                'Scopped contents:',
                '',
                '<comment>"""</comment>',
                $scoppedContents,
                '<comment>"""</comment>',
            ]);

            $io->writeln([
                '',
                'Symbols Registry:',
                '',
                '<comment>"""</comment>',
                self::exportSymbolsRegistry($symbolsRegistry, $io),
                '<comment>"""</comment>',
            ]);
        }
    }

    private static function exportSymbolsRegistry(SymbolsRegistry $symbolsRegistry, IO $io): string
    {
        $cloner = new VarCloner();
        $cloner->setMaxItems(-1);
        $cloner->setMaxString(-1);

        $cliDumper = new CliDumper();
        if ($io->isDecorated()) {
            $cliDumper->setColors(true);
        }

        return (string) $cliDumper->dump(
            $cloner->cloneVar($symbolsRegistry),
            true,
        );
    }
}
