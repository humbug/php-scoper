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

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use function file_exists;
use function getcwd;
use function sprintf;
use const DIRECTORY_SEPARATOR;

final class InitCommand extends BaseCommand
{
    private const CONFIG_FILE_OPT = 'config';
    private const CONFIG_FILE_TEMPLATE = __DIR__.'/../../scoper.inc.php.tpl';
    private const CONFIG_FILE_DEFAULT = 'scoper.inc.php';

    private $fileSystem;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->fileSystem = new Filesystem();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('init')
            ->setDescription('Generates a configuration file.')
            ->addOption(
                self::CONFIG_FILE_OPT,
                'c',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Configuration file. Will use "%s" if found by default.',
                    self::CONFIG_FILE_DEFAULT
                ),
                null
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->changeWorkingDirectory($input);

        $io = new SymfonyStyle($input, $output);
        $io->writeln('');

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $io->writeln(
            $formatter->formatSection(
                'PHP-Scoper configuration generate',
                'Welcome!'
            )
        );

        $configFile = $this->retrieveConfig($input, $io);

        if (null === $configFile) {
            $io->writeln('Skipping configuration file generator.');

            return 0;
        }

        $this->fileSystem->copy(self::CONFIG_FILE_TEMPLATE, $configFile);

        $io->writeln([
            '',
            sprintf(
                'Generated the configuration file "<comment>%s</comment>".',
                $configFile
            ),
            '',
        ]);

        return 0;
    }

    private function retrieveConfig(InputInterface $input, OutputStyle $io): ?string
    {
        /** @var string|null $configFile */
        $configFile = $input->getOption(self::CONFIG_FILE_OPT);

        $configFile = (null === $configFile)
            ? $this->makeAbsolutePath(self::CONFIG_FILE_DEFAULT)
            : $this->makeAbsolutePath($configFile)
        ;

        if (file_exists($configFile)) {
            $canDeleteFile = $io->confirm(
                sprintf(
                    'The configuration file "<comment>%s</comment>" already exists. Are you sure you want to '
                    .'replace it?',
                    $configFile
                ),
                false
            );

            if (false === $canDeleteFile) {
                $io->writeln('Skipped file generation.');

                return $configFile;
            }

            $this->fileSystem->remove($configFile);
        } else {
            $createConfig = $io->confirm('No configuration file found. Do you want to create one?');

            if (false === $createConfig) {
                return null;
            }
        }

        return $configFile;
    }

    private function makeAbsolutePath(string $path): string
    {
        if (false === $this->fileSystem->isAbsolutePath($path)) {
            $path = getcwd().DIRECTORY_SEPARATOR.$path;
        }

        return $path;
    }
}
