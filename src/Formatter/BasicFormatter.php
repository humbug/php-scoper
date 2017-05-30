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

namespace Webmozart\PhpScoper\Formatter;

use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PhpScoper\Console\Application;

class BasicFormatter
{
    /**
     * @var OutputInterface
     */
    protected $io;

    public function __construct(OutputInterface $output)
    {
        $this->io = $output;
    }

    public function outputScopingStart()
    {
        if (Application::VERSION == '@package_version@') {
            $version = '1.0-dev';
        } else {
            $version = Application::VERSION;
        }
        $this->io->writeLn(sprintf('PHP Scoper %s', $version));
    }

    public function outputFileCount(int $count)
    {
        if (0 === $count) {
            $this->io->writeLn('No PHP files to scope located with given path(s).');
        }
    }

    public function outputSuccess(string $path)
    {
        $this->io->writeLn(sprintf('Scoping %s. . . Success', $path));
    }

    public function outputFail(string $path)
    {
        $this->io->writeLn(sprintf('Scoping %s. . . Fail', $path));
    }

    public function outputScopingEnd()
    {

    }
}
