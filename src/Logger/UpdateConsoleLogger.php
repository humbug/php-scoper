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

namespace Humbug\PhpScoper\Logger;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @private
 * @final
 */
class UpdateConsoleLogger
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(Application $application, SymfonyStyle $io)
    {
        $this->io = $io;
        $this->application = $application;
    }

    public function startUpdating()
    {
        $this->io->writeln('Updating...');
    }

    public function updateSuccess(string $newVersion, string $oldVersion)
    {
        $this->io->writeln('PHP-Scoper has been updated.');
        $this->io->writeln(sprintf(
            'Current version is: %s.',
            $newVersion
        ));
        $this->io->writeln(sprintf(
            'Previous version was: %s.',
            $oldVersion
        ));
    }

    public function updateNotNeeded(string $oldVersion)
    {
        $this->io->writeln('PHP-Scoper is currently up to date.');
        $this->io->writeln(sprintf(
            'Current version is: %s.',
            $oldVersion
        ));
    }
    
    public function error(\Exception $e)
    {
        $this->io->writeln(sprintf('Error: %s', $e->getMessage()));
    }

    public function rollbackSuccess()
    {
        $this->io->writeln('PHP-Scoper has been rolled back to prior version.');
    }

    public function rollbackFail()
    {
        $this->io->writeln('Rollback failed for reasons unknown.');
    }

    public function printLocalVersion(string $version)
    {
        $this->io->writeln(sprintf(
            'Your current local version is: %s',
            $version
        ));
    }

    public function printRemoteVersion(string $stability, string $version)
    {
        $this->io->writeln(sprintf(
            'The current %s build available remotely is: %s',
            $stability,
            $version
        ));
    }

    public function noNewRemoteVersions(string $stability)
    {
        $this->io->writeln(sprintf('There are no new %s builds available.', $stability));
    }

    public function currentVersionInstalled(string $stability)
    {
        $this->io->writeln(sprintf('You have the current %s build installed.', $stability));
    }
}
