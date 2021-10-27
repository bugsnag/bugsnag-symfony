<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ErrorUnhandledCommand extends Command
{
    protected function configure()
    {
        $this->setName('app:error:unhandled');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foo();

        return 0;
    }
}
