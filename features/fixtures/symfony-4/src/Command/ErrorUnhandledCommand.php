<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ErrorUnhandledCommand extends Command
{
    protected static $defaultName = 'app:error:unhandled';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foo();

        return 0;
    }
}
