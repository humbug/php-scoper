<?php

declare(strict_types=1);

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

class HelloWorldCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('hello:world')
            ->setDescription('Outputs \'Hello World\'');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello world!');

        return self::SUCCESS;
    }
}

$command = new HelloWorldCommand();
$application = new Application();

$application->add($command);
$application->setDefaultCommand($command->getName());

$application->run();
