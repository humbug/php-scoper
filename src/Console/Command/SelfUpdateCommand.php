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

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SelfUpdateCommand extends Command
{
    /** @internal */
    const REMOTE_FILENAME = 'php-scoper.phar';
    /** @internal */
    const STABILITY_STABLE = 'stable';
    /** @internal */
    const PACKAGIST_PACKAGE_NAME = 'humbug/php-scoper';
    /** @internal */
    const ROLLBACK_OPT = 'rollback';
    /** @internal */
    const CHECK_OPT = 'check';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $version;

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update php-scoper.phar to most recent stable build.')
            ->addOption(
                self::ROLLBACK_OPT,
                'r',
                InputOption::VALUE_NONE,
                'Rollback to previous version of php-scoper if available on filesystem.'
            )
            ->addOption(
                self::CHECK_OPT,
                'c',
                InputOption::VALUE_NONE,
                'Checks whether an update is available.'
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->version = $this->getApplication()->getVersion();

        if ($input->getOption('rollback')) {
            $this->rollback();

            return;
        }

        if ($input->getOption('check')) {
            $this->printAvailableUpdates();

            return;
        }

        $this->updateToStableBuild();
    }

    private function updateToStableBuild()
    {
        $this->update($this->getStableUpdater());
    }

    /**
     * @return Updater
     */
    private function getStableUpdater(): Updater
    {
        $updater = new Updater();
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        return $this->getGithubReleasesUpdater($updater);
    }

    private function update(Updater $updater)
    {
        $this->output->writeln('Updating...'.PHP_EOL);
        try {
            $result = $updater->update();

            $newVersion = $updater->getNewVersion();
            $oldVersion = $updater->getOldVersion();
        
            if ($result) {
                $this->output->writeln('php-scoper has been updated.');
                $this->output->writeln(sprintf(
                    'Current version is: %s.',
                    $newVersion
                ));
                $this->output->writeln(sprintf(
                    'Previous version was: %s.',
                    $oldVersion
                ));
            } else {
                $this->output->writeln('php-scoper is currently up to date.');
                $this->output->writeln(sprintf(
                    'Current version is: %s.',
                    $oldVersion
                ));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: %s', $e->getMessage()));
        }
        $this->output->write(PHP_EOL);
    }

    private function rollback()
    {
        $updater = new Updater();
        try {
            $result = $updater->rollback();
            if ($result) {
                $this->output->writeln('php-scoper has been rolled back to prior version.');
            } else {
                $this->output->writeln('Rollback failed for reasons unknown.');
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: %s', $e->getMessage()));
        }
    }

    private function printAvailableUpdates()
    {
        $this->printCurrentLocalVersion();
        $this->printCurrentStableVersion();
    }

    private function printCurrentLocalVersion()
    {
        $this->output->writeln(sprintf(
            'Your current local version is: %s',
            $this->version
        ));
    }

    private function printCurrentStableVersion()
    {
        $this->printVersion($this->getStableUpdater());
    }

    /**
     * @param Updater $updater
     */
    private function printVersion(Updater $updater)
    {
        $stability = self::STABILITY_STABLE;
        try {
            if ($updater->hasUpdate()) {
                $this->output->writeln(sprintf(
                    'The current %s build available remotely is: %s',
                    $stability,
                    $updater->getNewVersion()
                ));
            } elseif (false == $updater->getNewVersion()) {
                $this->output->writeln(sprintf('There are no new %s builds available.', $stability));
            } else {
                $this->output->writeln(sprintf('You have the current %s build installed.', $stability));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: %s', $e->getMessage()));
        }
    }

    /**
     * @param Updater $updater
     * @return Updater
     */
    private function getGithubReleasesUpdater(Updater $updater): Updater
    {
        $updater->getStrategy()->setPackageName(self::PACKAGIST_PACKAGE_NAME);
        $updater->getStrategy()->setPharName(self::REMOTE_FILENAME);
        $updater->getStrategy()->setCurrentLocalVersion($this->version);
        return $updater;
    }
}
