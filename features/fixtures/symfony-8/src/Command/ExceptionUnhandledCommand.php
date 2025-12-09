<?php

namespace App\Command;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:exception:unhandled',
    description: 'Trigger an unhandled exception',
)]
class ExceptionUnhandledCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        throw new RuntimeException('Crashing exception in a command!');

        return Command::SUCCESS;
    }
}
