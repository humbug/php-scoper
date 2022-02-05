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
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use function array_map;
use function implode;
use function in_array;
use function is_dir;
use function is_writable;
use function Safe\getcwd;
use function Safe\sprintf;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class InspectSymbolCommand implements Command, CommandAware
{
    use CommandAwareness;

    private const SYMBOL_ARG = 'symbol';
    private const SYMBOL_TYPE_ARG = 'type';
    private const CONFIG_FILE_OPT = 'config';
    private const DEFAULT_CONFIG_FILE_PATH = 'scoper.inc.php';
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
        $io->newLine();

        ChangeableDirectory::changeWorkingDirectory($io);

        $symbol = $io->getStringArgument(self::SYMBOL_ARG);
        $symbolType = self::getSymbolType($io);
        $config = $this->retrieveConfig($io);

        $enrichedReflector = $this->enrichedReflectorFactory->create(
            $config->getSymbolsConfiguration(),
        );

        self::printSymbol($io, $symbol, $symbolType, $enrichedReflector);

        return ExitCode::SUCCESS;
    }

    /**
     * @return SymbolType::*_TYPE
     */
    private static function getSymbolType(IO $io): string
    {
        // TODO: use options when available https://github.com/theofidry/console/issues/18
        $type = $io->getStringArgument(self::SYMBOL_TYPE_ARG);

        if (!in_array($type, SymbolType::ALL, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected symbol type to be one of "%s". Got "%s"',
                    implode('", "', SymbolType::ALL),
                    $type,
                ),
            );
        }

        return $type;
    }

    /**
     * @param string[] $paths
     */
    private function retrieveConfig(IO $io): Configuration
    {
        // TODO: check that it doesn't trigger the init
        $configLoader = new ConfigLoader(
            $this->getCommandRegistry(),
            $this->fileSystem,
            $this->configFactory,
        );

        return $configLoader->loadConfig(
            $io,
            '',
            $io->getBooleanOption(self::NO_CONFIG_OPT),
            $io->getNullableStringOption(self::CONFIG_FILE_OPT),
            self::DEFAULT_CONFIG_FILE_PATH,
            false,
            [],
            getcwd(),
        );
    }

    /**
     * @param SymbolType::*_TYPE $type
     */
    private static function printSymbol(
        IO $io,
        string $symbol,
        string $type,
        EnrichedReflector $reflector
    ): void
    {
        $isTypeAny = SymbolType::ANY_TYPE === $type;

        $io->writeln([
            'Internal (configured via the `excluded-*` settings are treated as PHP native symbols, i.e. will remain untouched.',
            'Exposed symbols will be prefixed but aliased to its original symbol.',
            'If a symbol is neither internal or exposed, it will be prefixed and not aliased',
            '',
            'For more information, see:'
        ]);
        $io->listing([
            '<href=https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#excluded-symbols>Doc link for excluded symbols</>',
            '<href=https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#exposed-symbols>Doc link for exposed symbols</>',
        ]);

        $io->writeln(
            sprintf(
                'Inspecting the symbol <comment>%s</comment> %s',
                $symbol,
                $isTypeAny
                    ? 'for all types.'
                    : 'for type <comment>%s</comment>:',
            ),
        );

        $io->newLine();

        if (!$isTypeAny) {
            self::printTypedSymbol($io, $symbol, $type, $reflector);

            return;
        }

        foreach (SymbolType::getAllSpecificTypes() as $type) {
            $io->writeln(
                sprintf(
                    'As a <comment>%s</comment>:',
                    $type,
                ),
            );

            self::printTypedSymbol($io, $symbol, $type, $reflector);
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
    ): void
    {
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
                    $reflector->isClassInternal($symbol),
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
}
