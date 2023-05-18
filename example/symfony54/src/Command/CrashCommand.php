<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrashCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:crash';

    protected function configure()
    {
        $this->setDescription('Crashes for fun');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new Exception('Something bad happened!');
    }
}
