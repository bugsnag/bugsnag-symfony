<?php

namespace App\Command;

use Bugsnag\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:error:handled',
    description: 'Notify Bugsnag of a handled error',
)]
class ErrorHandledCommand extends Command
{
    private Client $bugsnag;

    public function __construct(Client $bugsnag)
    {
        parent::__construct();

        $this->bugsnag = $bugsnag;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bugsnag->notifyError(
            'Handled error',
            'This is a handled error in a command'
        );

        $output->writeln('Notfied of a handled error!');

        return Command::SUCCESS;
    }
}
