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

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @private
 * @final
 */
class UpdateConsoleLogger
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    public function startUpdating()
    {
        $this->io->writeln('Updating...');
    }

    public function updateSuccess(string $newVersion, string $oldVersion)
    {
        $this->io->writeln('PHP-Scoper has been updated.');
        $this->io->writeln(sprintf(
            'Current version is: <comment>%s</comment>.',
            $newVersion
        ));
        $this->io->writeln(sprintf(
            'Previous version was: <comment>%s</comment>.',
            $oldVersion
        ));
    }

    public function updateNotNeeded(string $oldVersion)
    {
        $this->io->writeln('PHP-Scoper is currently up to date.');
        $this->io->writeln(sprintf(
            'Current version is: <comment>%s</comment>.',
            $oldVersion
        ));
    }

    public function error(\Exception $e)
    {
        $this->io->writeln(
            '<error>Unexpected error. If updating, your original phar is untouched.</error>'
        );
        $this->io->writeln(sprintf('<error>%s</error>', $e->getMessage()));
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
            'Your current local version is: <comment>%s</comment>',
            $version
        ));
    }

    public function printRemoteVersion(string $stability, string $version)
    {
        $this->io->writeln(sprintf(
            'The current <comment>%s</comment> build available remotely is: <comment>%s</comment>',
            $stability,
            $version
        ));
    }

    public function noNewRemoteVersions(string $stability)
    {
        $this->io->writeln(sprintf('There are no new <comment>%s</comment> builds available.', $stability));
    }

    public function currentVersionInstalled(string $stability)
    {
        $this->io->writeln(sprintf('You have the current <comment>%s</comment> build installed.', $stability));
    }
}
