<?php

namespace App\Command;

use Bugsnag\Client;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:exception:handled',
    description: 'Notify Bugsnag of a handled exception',
)]
class ExceptionHandledCommand extends Command
{
    private Client $bugsnag;

    public function __construct(Client $bugsnag)
    {
        parent::__construct();

        $this->bugsnag = $bugsnag;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bugsnag->notifyException(
            new LogicException('This is a handled exception in a command')
        );

        $output->writeln('Notfied of a handled exception!');

        return Command::SUCCESS;
    }
}
