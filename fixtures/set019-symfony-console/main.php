<?php

declare(strict_types=1);

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/vendor/autoload.php';

class HelloWorldCommand extends Command
{
    protected function configure()
    {
        $this->setName('hello:world')
            ->setDescription('Outputs \'Hello World\'');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello world!');
    }
}

$command = new HelloWorldCommand();
$application = new Application();

$application->add($command);
$application->setDefaultCommand($command->getName());

$application->run();
