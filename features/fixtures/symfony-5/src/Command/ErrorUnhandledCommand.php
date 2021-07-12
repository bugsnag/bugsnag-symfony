<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:error:unhandled',
    description: 'Trigger an unhandled error',
)]
class ErrorUnhandledCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foo();

        return Command::SUCCESS;
    }
}
