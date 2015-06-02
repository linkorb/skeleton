<?php

namespace LinkORB\Skeleton\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

class ExampleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('skeleton:example')
            ->setDescription('An example command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Cool!</info>');
    }
}
