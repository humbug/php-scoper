<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Command\CommandRegistry;
use Fidry\Console\IO;
use Humbug\PhpScoper\Configuration;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use function bin2hex;
use function count;
use function file_exists;
use function random_bytes;
use function Safe\sprintf;
use function trim;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class ConfigLoader
{
    private CommandRegistry $commandRegistry;
    private Filesystem $fileSystem;

    public function __construct(
        CommandRegistry $commandRegistry,
        Filesystem $fileSystem
    ) {
        $this->commandRegistry = $commandRegistry;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param string[] $paths
     */
    public function loadConfig(
        IO $io,
        string $prefix,
        bool $noConfig,
        ?string $configFilePath,
        string $defaultConfigFilePath,
        bool $isInitCommandExecuted,
        array $paths,
        string $cwd
    ): Configuration
    {
        $prefix = trim($prefix);

        if ($noConfig) {
            return self::loadConfigWithoutConfigFile(
                $io,
                $prefix,
                $paths,
                $cwd,
            );
        }

        if (null === $configFilePath && !$isInitCommandExecuted) {
            $configFilePath = $this->loadDefaultConfig(
                $io,
                $this->makeAbsolutePath($defaultConfigFilePath, $cwd),
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
        } elseif (null !== $configFilePath) {
            $configFilePath = $this->makeAbsolutePath(
                $configFilePath,
                $cwd,
            );
        }

        self::logConfigFilePathFound($io, $configFilePath);

        return self::loadConfiguration($configFilePath, $prefix, $paths, $cwd);
    }

    /**
     * @param string[] $paths
     */
    private static function loadConfigWithoutConfigFile(
        IO $io,
        string $prefix,
        array $paths,
        string $cwd
    ): Configuration
    {
        $io->writeln(
            'Loading without configuration file.',
            OutputInterface::VERBOSITY_DEBUG
        );

        return self::loadConfiguration(null, $prefix, $paths, $cwd);
    }

    /**
     * @return string|null Config file path when found otherwise executes the init command
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

        if (false === file_exists($configFilePath)) {
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
     * @param string[] $paths
     */
    private static function loadConfiguration(
        ?string $configFilePath,
        string $prefix,
        array $paths,
        string $cwd
    ): Configuration
    {
        return self::configurePaths(
            self::configurePrefix(
                Configuration::load($configFilePath, $paths),
                $prefix,
            ),
            $cwd,
        );
    }

    private static function configurePrefix(Configuration $config, string $prefix): Configuration
    {
        if ('' !== $prefix) {
            return $config->withPrefix($prefix);
        }

        if (null === $config->getPrefix()) {
            return $config->withPrefix(self::generateRandomPrefix());
        }

        return $config;
    }

    private static function configurePaths(
        Configuration $config,
        string $cwd
    ): Configuration
    {
        // Use the current working directory as the path if no file has been
        // found
        if (0 === count($config->getFilesWithContents())) {
            return $config->withPaths([$cwd]);
        }

        return $config;
    }

    private static function generateRandomPrefix(): string
    {
        return '_PhpScoper'.bin2hex(random_bytes(6));
    }

    private function makeAbsolutePath(
        string $path,
        string $cwd
    ): string
    {
        if (false === $this->fileSystem->isAbsolutePath($path)) {
            $path = $cwd.DIRECTORY_SEPARATOR.$path;
        }

        return $path;
    }
}
