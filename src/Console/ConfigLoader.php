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

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Command\CommandRegistry;
use Fidry\Console\IO;
use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Configuration\Throwable\InvalidConfigurationValue;
use Humbug\PhpScoper\Configuration\Throwable\UnknownConfigurationKey;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use function assert;
use function count;
use function file_exists;
use function sprintf;
use function trim;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final readonly class ConfigLoader
{
    public function __construct(
        private CommandRegistry $commandRegistry,
        private Filesystem $fileSystem,
        private ConfigurationFactory $configFactory,
    ) {
    }

    /**
     * @param non-empty-string|null  $configFilePath        Canonical absolute path
     * @param non-empty-string       $defaultConfigFilePath
     * @param list<non-empty-string> $paths                 List of canonical absolute paths
     *
     * @throws InvalidConfigurationValue
     * @throws UnknownConfigurationKey
     */
    public function loadConfig(
        IO $io,
        string $prefix,
        bool $noConfig,
        ?string $configFilePath,
        string $defaultConfigFilePath,
        bool $isInitCommandExecuted,
        array $paths,
        string $cwd,
    ): Configuration {
        $prefix = trim($prefix);
        $defaultConfigFilePath = $this->canonicalizePath($defaultConfigFilePath, $cwd);

        if ($noConfig) {
            return $this->loadConfigWithoutConfigFile(
                $io,
                $prefix,
                $paths,
                $cwd,
            );
        }

        if (null === $configFilePath && !$isInitCommandExecuted) {
            $configFilePath = $this->loadDefaultConfig(
                $io,
                $defaultConfigFilePath,
            );

            if (null === $configFilePath) {
                return $this->loadConfig(
                    $io,
                    $prefix,
                    $noConfig,
                    $configFilePath,
                    $defaultConfigFilePath,
                    true,
                    $paths,
                    $cwd,
                );
            }
        }

        self::logConfigFilePathFound($io, $configFilePath);

        return $this->loadConfiguration($configFilePath, $prefix, $paths, $cwd);
    }

    /**
     * @param list<non-empty-string> $paths
     *
     * @throws InvalidConfigurationValue
     * @throws UnknownConfigurationKey
     */
    private function loadConfigWithoutConfigFile(
        IO $io,
        string $prefix,
        array $paths,
        string $cwd,
    ): Configuration {
        $io->writeln(
            'Loading without configuration file.',
            OutputInterface::VERBOSITY_DEBUG,
        );

        return $this->loadConfiguration(null, $prefix, $paths, $cwd);
    }

    /**
     * @param non-empty-string $defaultConfigFilePath
     *
     * @return non-empty-string|null Config file path when found otherwise executes the init command
     */
    private function loadDefaultConfig(IO $io, string $defaultConfigFilePath): ?string
    {
        $configFilePath = $defaultConfigFilePath;

        if (file_exists($configFilePath)) {
            return $configFilePath;
        }

        $initInput = new StringInput('');
        $initInput->setInteractive($io->isInteractive());

        $this->commandRegistry
            ->getCommand('init')
            ->execute(
                new IO(
                    $initInput,
                    $io->getOutput(),
                ),
            );

        $io->writeln(
            sprintf(
                'Config file "<comment>%s</comment>" not found. Skipping.',
                $configFilePath,
            ),
            OutputInterface::VERBOSITY_DEBUG,
        );

        return null;
    }

    private static function logConfigFilePathFound(IO $io, ?string $configFilePath): void
    {
        if (null === $configFilePath) {
            $io->writeln(
                'Loading without configuration file.',
                OutputInterface::VERBOSITY_DEBUG,
            );

            return;
        }

        if (!file_exists($configFilePath)) {
            throw new RuntimeException(
                sprintf(
                    'Could not find the configuration file "%s".',
                    $configFilePath,
                ),
            );
        }

        $io->writeln(
            sprintf(
                'Using the configuration file "%s".',
                $configFilePath,
            ),
            OutputInterface::VERBOSITY_DEBUG,
        );
    }

    /**
     * @param non-empty-string|null  $configFilePath
     * @param list<non-empty-string> $paths
     *
     * @throws InvalidConfigurationValue
     * @throws UnknownConfigurationKey
     */
    private function loadConfiguration(
        ?string $configFilePath,
        string $prefix,
        array $paths,
        string $cwd,
    ): Configuration {
        return $this->configurePaths(
            $this->configurePrefix(
                $this->configFactory->create($configFilePath, $paths),
                $prefix,
            ),
            $cwd,
        );
    }

    private function configurePrefix(Configuration $config, string $prefix): Configuration
    {
        return '' !== $prefix
            ? $this->configFactory->createWithPrefix(
                $config,
                $prefix,
            )
            : $config;
    }

    private function configurePaths(
        Configuration $config,
        string $cwd,
    ): Configuration {
        // Use the current working directory as the path if no file has been
        // found
        return 0 === count($config->getFilesWithContents())
            ? $this->configFactory->createWithPaths(
                $config,
                [$cwd],
            )
            : $config;
    }

    /**
     * @param non-empty-string $path
     *
     * @return non-empty-string Absolute canonical path
     */
    private function canonicalizePath(string $path, string $cwd): string
    {
        $canonicalPath = Path::canonicalize(
            $this->fileSystem->isAbsolutePath($path)
                ? $path
                : $cwd.DIRECTORY_SEPARATOR.$path,
        );

        assert('' !== $canonicalPath);

        return $canonicalPath;
    }
}
