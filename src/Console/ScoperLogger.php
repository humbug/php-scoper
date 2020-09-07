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

use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @private
 * @final
 */
class ScoperLogger
{
    private $application;
    private $io;
    private $startTime;
    private $progressBar;
    private $prefixedFilesCount;
    private $totalProcessedFilesCount;

    public function __construct(SymfonyApplication $application, SymfonyStyle $io)
    {
        $this->io = $io;
        $this->application = $application;
        $this->startTime = microtime(true);
        $this->progressBar = new ProgressBar(new NullOutput());
    }

    /**
     * @param string   $prefix
     * @param string[] $paths
     */
    public function outputScopingStart(string $prefix, array $paths): void
    {
        $this->prefixedFilesCount       = 0;
        $this->totalProcessedFilesCount = 0;

        $this->io->writeln($this->application->getHelp());

        $newLine = 1;

        if ($this->io->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
            $this->io->section('Input');

            $this->io->writeln(
                sprintf(
                    'Prefix: %s',
                    $prefix
                )
            );

            $this->io->write('Paths:');

            if (0 === count($paths)) {
                $this->io->writeln(' Loaded from config');
            } else {
                $this->io->writeln('');
                $this->io->listing($paths);
            }

            $this->io->section('Processing');
            $newLine = 0;
        }

        $this->io->newLine($newLine);
    }

    /**
     * Output file count message if relevant.
     *
     * @param int $count
     */
    public function outputFileCount(int $count): void
    {
        if (OutputInterface::VERBOSITY_NORMAL === $this->io->getVerbosity()) {
            $this->progressBar = $this->io->createProgressBar($count);
            $this->progressBar->start();
        } elseif ($this->io->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->progressBar = new ProgressBar(new NullOutput());
        }
    }

    /**
     * Output scoping success message.
     *
     * @param string $path
     * @param bool $fileWasPrefixed True if the file was prefixed, false if it was just copied
     */
    public function outputSuccess(string $path, bool $fileWasPrefixed): void
    {
        if ($this->io->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->io->writeln(
                sprintf(
                    ' * [<info>OK</info>] %s',
                    $path
                )
            );
        }

        if($fileWasPrefixed) {
            $this->prefixedFilesCount++;
        }
        $this->totalProcessedFilesCount++;

        $this->progressBar->advance();
    }

    public function outputWarnOfFailure(string $path, ParsingException $exception): void
    {
        if ($this->io->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->io->writeln(
                sprintf(
                    ' * [<error>NO</error>] %s',
                    $path
                )
            );
        }

        if ($this->io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $this->io->writeln(
                sprintf(
                    "\t".'%s: %s',
                    $exception->getMessage(),
                    (string) $exception->getPrevious()
                )
            );
        }

        $this->prefixedFilesCount++;

        $this->progressBar->advance();
    }

    public function outputScopingEnd(): void
    {
        $this->finish(false);
    }

    public function outputScopingEndWithFailure(): void
    {
        $this->finish(true);
    }

    private function finish(bool $failed): void
    {
        $this->progressBar->finish();
        $this->io->newLine(2);

        if (false === $failed) {
            if($this->totalProcessedFilesCount === $this->prefixedFilesCount) {
                $message = sprintf(
                    'Successfully prefixed %d files.',
                    $this->prefixedFilesCount
                );
            }
            else {
                $message = sprintf(
                    'Successfully processed %d files, of which %d were prefixed.',
                    $this->totalProcessedFilesCount,
                    $this->prefixedFilesCount
                );
            }

            $this->io->success($message);
        }

        if ($this->io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->io->comment(
                sprintf(
                    '<info>Memory usage: %.2fMB (peak: %.2fMB), time: %.2fs<info>',
                    round(memory_get_usage() / 1024 / 1024, 2),
                    round(memory_get_peak_usage() / 1024 / 1024, 2),
                    round(microtime(true) - $this->startTime, 2)
                )
            );
        }
    }
}
