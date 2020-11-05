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

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use function chdir;
use function file_exists;
use function getcwd;
use function sprintf;

abstract class BaseCommand extends Command
{
    private const WORKING_DIR_OPT = 'working-dir';

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->addOption(
            self::WORKING_DIR_OPT,
            'd',
            InputOption::VALUE_REQUIRED,
            'If specified, use the given directory as working directory.',
            null
        );
    }

    final public function changeWorkingDirectory(InputInterface $input): void
    {
        /** @var string|null $workingDir */
        $workingDir = $input->getOption(self::WORKING_DIR_OPT);

        if (null === $workingDir) {
            return;
        }

        if (false === file_exists($workingDir)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Could not change the working directory to "%s": directory does not exists.',
                    $workingDir
                )
            );
        }

        if (false === chdir($workingDir)) {
            throw new RuntimeException(
                sprintf(
                    'Failed to change the working directory to "%s" from "%s".',
                    $workingDir,
                    getcwd()
                )
            );
        }
    }
}
