<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Humbug\PhpScoper\Logger\ConsoleLogger;
use Humbug\PhpScoper\Handler\HandleAddPrefix;

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
    public function __construct()
    {
        parent::__construct();

        $this->fileSystem = new Filesystem();
        $this->handle = new HandleAddPrefix();
    }


    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('add-prefix')
            ->setDescription('Goes through all the PHP files found in the given paths to apply the given prefix.')
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

        $logger = new ConsoleLogger($this->getApplication(), $output);

        $logger->outputScopingStart();
        $this->handle->__invoke(
            $input->getArgument(self::PREFIX_ARG),
            $input->getArgument(self::PATH_ARG),
            $logger
        );
        $logger->outputScopingEnd();
    }

    private function validatePrefix(InputInterface $input)
    {
        $prefix = trim($input->getArgument(self::PREFIX_ARG));

        if ('' === $prefix) {
            throw new RuntimeException(
                sprintf(
                    'Expected "%s" argument to be a non empty string.',
                    self::PREFIX_ARG
                )
            );
        }

        if ('\\' !== substr($prefix, -1)) {
            $prefix .= '\\';
        }

        $input->setArgument(self::PREFIX_ARG, $prefix);
    }

    private function validatePaths(InputInterface $input)
    {
        $cwd = getcwd();
        $fileSystem = $this->fileSystem;

        array_map(
            function (string $path) use ($cwd, $fileSystem) {
                if (false === file_exists($path)) {
                    throw new RuntimeException(
                        sprintf(
                            'Could not find the path "%s".',
                            $path
                        )
                    );
                }

                if (false === is_readable($path)) {
                    throw new RuntimeException(
                        sprintf(
                            'Could not read the path "%s".',
                            $path
                        )
                    );
                }

                if (false === $fileSystem->isAbsolutePath($path)) {
                    return $cwd.DIRECTORY_SEPARATOR.$path;
                }

                return $path;
            },
            $input->getArgument(self::PATH_ARG)
        );
    }
}