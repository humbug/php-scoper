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

use Humbug\PhpScoper\Handler\HandleAddPrefix;
use Humbug\PhpScoper\Logger\ConsoleLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class AddPrefixCommand extends Command
{
    /** @private */
    const PREFIX_ARG = 'prefix';
    /** @private */
    const PATH_ARG = 'paths';

    private $fileSystem;
    private $handle;

    /**
     * @inheritdoc
     */
    public function __construct(HandleAddPrefix $handle)
    {
        parent::__construct();

        $this->fileSystem = new Filesystem();
        $this->handle = $handle;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('add-prefix')
            ->setDescription('Goes through all the PHP files found in the given paths to apply the given prefix to namespaces & FQNs.')
            ->addArgument(self::PREFIX_ARG, InputArgument::REQUIRED, 'The namespace prefix to add')
            ->addArgument(self::PATH_ARG, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The path(s) to process.')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validatePrefix($input);
        $this->validatePaths($input);

        $logger = new ConsoleLogger(
            $this->getApplication(),
            new SymfonyStyle($input, $output)
        );

        $logger->outputScopingStart(
            $input->getArgument(self::PREFIX_ARG),
            $input->getArgument(self::PATH_ARG)
        );

        try {
            $this->handle->__invoke(
                $input->getArgument(self::PREFIX_ARG),
                $input->getArgument(self::PATH_ARG),
                $logger
            );
        } catch (\Throwable $throwable) {
            $logger->outputScopingEndWithFailure();

            throw $throwable;
        }

        $logger->outputScopingEnd();
    }

    private function validatePrefix(InputInterface $input)
    {
        $prefix = trim($input->getArgument(self::PREFIX_ARG));

        if (1 === preg_match('/(?<prefix>.*?)\\\\*$/', $prefix, $matches)) {
            $prefix = $matches['prefix'];
        }

        if ('' === $prefix) {
            throw new RuntimeException(
                sprintf(
                    'Expected "%s" argument to be a non empty string.',
                    self::PREFIX_ARG
                )
            );
        }

        $input->setArgument(self::PREFIX_ARG, $prefix);
    }

    private function validatePaths(InputInterface $input)
    {
        $cwd = getcwd();
        $fileSystem = $this->fileSystem;

        $paths = array_map(
            function (string $path) use ($cwd, $fileSystem) {
                if (false === $fileSystem->isAbsolutePath($path)) {
                    return $cwd.DIRECTORY_SEPARATOR.$path;
                }

                return $path;
            },
            $input->getArgument(self::PATH_ARG)
        );

        $input->setArgument(self::PATH_ARG, $paths);
    }
}
