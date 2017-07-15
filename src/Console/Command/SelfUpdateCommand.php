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

use Humbug\PhpScoper\Logger\UpdateConsoleLogger;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
     * @var Updater
     */
    private $updater;

    /**
     * @var string
     */
    private $version;

    /**
     * @var UpdateConsoleLogger
     */
    private $logger;

    /**
     * @param Updater $updater
     */
    public function __construct(Updater $updater)
    {
        parent::__construct();

        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription(sprintf(
                    'Update %s to most recent stable build.',
                    $this->getLocalPharName()
            ))
            ->addOption(
                self::ROLLBACK_OPT,
                'r',
                InputOption::VALUE_NONE,
                'Rollback to previous version of PHP-Scoper if available on filesystem.'
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
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new UpdateConsoleLogger(
            new SymfonyStyle($input, $output)
        );

        $this->version = $this->getApplication()->getVersion();

        $this->configureUpdater();

        if ($input->getOption('rollback')) {
            $this->rollback();

            return 0;
        }

        if ($input->getOption('check')) {
            $this->printAvailableUpdates();

            return 0;
        }

        $this->update();
    }

    private function configureUpdater()
    {
        $this->updater->setStrategy(Updater::STRATEGY_GITHUB);
        $this->updater->getStrategy()->setPackageName(self::PACKAGIST_PACKAGE_NAME);
        $this->updater->getStrategy()->setPharName(self::REMOTE_FILENAME);
        $this->updater->getStrategy()->setCurrentLocalVersion($this->version);
    }

    private function update()
    {
        $this->logger->startUpdating();
        try {
            $result = $this->updater->update();

            $newVersion = $this->updater->getNewVersion();
            $oldVersion = $this->updater->getOldVersion();

            if ($result) {
                $this->logger->updateSuccess($newVersion, $oldVersion);
            } else {
                $this->logger->updateNotNeeded($oldVersion);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e);
            throw $e;
        }
    }

    private function rollback()
    {
        try {
            $result = $this->updater->rollback();
            if ($result) {
                $this->logger->rollbackSuccess();
            } else {
                $this->logger->rollbackFail();
            }
        } catch (\Throwable $e) {
            $this->logger->error($e);
            throw $e;
        }
    }

    private function printAvailableUpdates()
    {
        $this->logger->printLocalVersion($this->version);
        $this->printCurrentStableVersion();
    }

    private function printCurrentStableVersion()
    {
        $stability = self::STABILITY_STABLE;

        try {
            if ($this->updater->hasUpdate()) {
                $this->logger->printRemoteVersion(
                    $stability,
                    $updater->getNewVersion()
                );
            } elseif (false == $this->updater->getNewVersion()) {
                $this->logger->noNewRemoteVersions($stability);
            } else {
                $this->logger->currentVersionInstalled($stability);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e);
            throw $e;
        }
    }

    private function getLocalPharName(): string
    {
        return basename(\PHAR::running());
    }
}
