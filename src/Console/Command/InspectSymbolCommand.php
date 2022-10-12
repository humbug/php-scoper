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
use Fidry\Console\Command\CommandRegistry;
use Fidry\Console\Command\Configuration as CommandConfiguration;
use Fidry\Console\ExitCode;
use Fidry\Console\Input\IO;
use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Console\ConfigLoader;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use InvalidArgumentException;
use Symfony\Component\Console\Application as DummyApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use function assert;
use function file_exists;
use function implode;
use function Safe\getcwd;
use function Safe\sprintf;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class InspectSymbolCommand implements Command
{
    private const SYMBOL_ARG = 'symbol';
    private const SYMBOL_TYPE_ARG = 'type';
    private const CONFIG_FILE_OPT = 'config';
    private const NO_CONFIG_OPT = 'no-config';

    private Filesystem $fileSystem;
    private ConfigurationFactory $configFactory;
    private EnrichedReflectorFactory $enrichedReflectorFactory;

    public function __construct(
        Filesystem $fileSystem,
        ConfigurationFactory $configFactory,
        EnrichedReflectorFactory $enrichedReflectorFactory
    ) {
        $this->fileSystem = $fileSystem;
        $this->configFactory = $configFactory;
        $this->enrichedReflectorFactory = $enrichedReflectorFactory;
    }

    public function getConfiguration(): CommandConfiguration
    {
        return new CommandConfiguration(
            'inspect-symbol',
            'Checks the given symbol for a given configuration. Helpful to have an insight on how PHP-Scoper will interpret this symbol',
            '',
            [
                new InputArgument(
                    self::SYMBOL_ARG,
                    InputArgument::REQUIRED,
                    'The symbol to inspect.'
                ),
                new InputArgument(
                    self::SYMBOL_TYPE_ARG,
                    InputArgument::OPTIONAL,
                    sprintf(
                        'The symbol type inspect ("%s").',
                        implode('", "', SymbolType::ALL),
                    ),
                    SymbolType::ANY_TYPE,
                ),
            ],
            [
                ChangeableDirectory::createOption(),
                new InputOption(
                    self::CONFIG_FILE_OPT,
                    'c',
                    InputOption::VALUE_REQUIRED,
                    sprintf(
                        'Configuration file. Will use "%s" if found by default.',
                        ConfigurationFactory::DEFAULT_FILE_NAME
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

        $symbol = $io->getArgument(self::SYMBOL_ARG)->asString();
        /** @var SymbolType::*_TYPE $symbolType */
        $symbolType = $io->getArgument(self::SYMBOL_TYPE_ARG)->asStringChoice(SymbolType::ALL);
        $config = $this->retrieveConfig($io, $cwd);

        $enrichedReflector = $this->enrichedReflectorFactory->create(
            $config->getSymbolsConfiguration(),
        );

        self::printSymbol(
            $io,
            $symbol,
            $symbolType,
            $config->getPath(),
            $enrichedReflector,
        );

        return ExitCode::SUCCESS;
    }

    private function retrieveConfig(IO $io, string $cwd): Configuration
    {
        $configLoader = new ConfigLoader(
            new CommandRegistry(new DummyApplication()),
            $this->fileSystem,
            $this->configFactory,
        );

        $configFilePath = $this->getConfigFilePath($io, $cwd);
        $noConfig = $io->getOption(self::NO_CONFIG_OPT)->asBoolean();

        if (null === $configFilePath) {
            // Unlike when scoping, we do not want a config file to be created
            // neither bother the user with passing the no-config option if the
            // file does not exist.
            $noConfig = true;
        }

        return $configLoader->loadConfig(
            new IO(
                $io->getInput(),
                new NullOutput(),
            ),
            '',
            $noConfig,
            $configFilePath,
            ConfigurationFactory::DEFAULT_FILE_NAME,
            // We do not want the init command to be triggered if there is no
            // config file.
            true,
            [],
            getcwd(),
        );
    }

    /**
     * @return non-empty-string|null
     */
    private function getConfigFilePath(IO $io, string $cwd): ?string
    {
        $configPath = (string) $io->getOption(self::CONFIG_FILE_OPT)->asNullableString();

        if ('' === $configPath) {
            $configPath = ConfigurationFactory::DEFAULT_FILE_NAME;
        }

        $configPath = $this->canonicalizePath($configPath, $cwd);

        return file_exists($configPath) ? $configPath : null;
    }

    /**
     * @param SymbolType::*_TYPE $type
     */
    private static function printSymbol(
        IO $io,
        string $symbol,
        string $type,
        ?string $configPath,
        EnrichedReflector $reflector
    ): void {
        self::printDocBlock($io);
        self::printConfigLoaded($io, $configPath);
        self::printInspectionHeadline($io, $symbol, $type);

        $io->newLine();

        if (!(SymbolType::ANY_TYPE === $type)) {
            self::printTypedSymbol($io, $symbol, $type, $reflector);
        } else {
            self::printAnyTypeSymbol($io, $symbol, $reflector);
        }
    }

    private static function printDocBlock(IO $io): void
    {
        $io->writeln([
            'Internal (configured via the `excluded-*` settings) are treated as PHP native symbols, i.e. will remain untouched.',
            'Exposed symbols (configured via the `expose-*` settings) will be prefixed but aliased to its original symbol.',
            'If a symbol is neither internal or exposed, it will be prefixed and not aliased',
            '',
            'For more information, see:',
        ]);
        $io->listing([
            '<href=https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#excluded-symbols>Doc link for excluded symbols</>',
            '<href=https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#exposed-symbols>Doc link for exposed symbols</>',
        ]);
    }

    private static function printConfigLoaded(IO $io, ?string $configPath): void
    {
        $io->writeln(
            null === $configPath
                ? 'No configuration loaded.'
                : sprintf(
                    'Loaded the configuration <comment>%s</comment>',
                    $configPath,
                ),
        );
        $io->newLine();
    }

    /**
     * @param SymbolType::*_TYPE $type
     */
    private static function printInspectionHeadline(
        IO $io,
        string $symbol,
        string $type
    ): void {
        $io->writeln(
            sprintf(
                'Inspecting the symbol <comment>%s</comment> %s',
                $symbol,
                SymbolType::ANY_TYPE === $type
                    ? 'for all types.'
                    : sprintf('for type <comment>%s</comment>:', $type),
            ),
        );
    }

    private static function printAnyTypeSymbol(
        IO $io,
        string $symbol,
        EnrichedReflector $reflector
    ): void {
        foreach (SymbolType::getAllSpecificTypes() as $specificType) {
            $io->writeln(
                sprintf(
                    'As a <comment>%s</comment>:',
                    $specificType,
                ),
            );

            self::printTypedSymbol($io, $symbol, $specificType, $reflector);
        }
    }

    /**
     * @param SymbolType::*_TYPE $type
     */
    private static function printTypedSymbol(
        IO $io,
        string $symbol,
        string $type,
        EnrichedReflector $reflector
    ): void {
        [$internal, $exposed] = self::determineSymbolStatus(
            $symbol,
            $type,
            $reflector,
        );

        $io->listing([
            sprintf(
                'Internal: %s',
                self::convertBoolToString($internal),
            ),
            sprintf(
                'Exposed:  %s',
                self::convertBoolToString($exposed),
            ),
        ]);
    }

    /**
     * @param SymbolType::*_TYPE $type
     */
    private static function determineSymbolStatus(
        string $symbol,
        string $type,
        EnrichedReflector $reflector
    ): array {
        switch ($type) {
            case SymbolType::CLASS_TYPE:
                return [
                    $reflector->isClassInternal($symbol),
                    $reflector->isExposedClass($symbol),
                ];

            case SymbolType::FUNCTION_TYPE:
                return [
                    $reflector->isFunctionInternal($symbol),
                    $reflector->isExposedFunction($symbol),
                ];

            case SymbolType::CONSTANT_TYPE:
                return [
                    $reflector->isConstantInternal($symbol),
                    $reflector->isExposedConstant($symbol),
                ];
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid type "%s"',
                $type,
            ),
        );
    }

    private static function convertBoolToString(bool $bool): string
    {
        return true === $bool ? '<question>true</question>' : '<error>false</error>';
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
