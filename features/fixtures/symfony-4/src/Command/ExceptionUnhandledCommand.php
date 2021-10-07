<?php

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExceptionUnhandledCommand extends Command
{
    protected static $defaultName = 'app:exception:unhandled';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        throw new RuntimeException('Crashing exception in a command!');

        return 0;
    }
}
