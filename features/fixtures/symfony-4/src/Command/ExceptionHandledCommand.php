<?php

namespace App\Command;

use Bugsnag\Client;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExceptionHandledCommand extends Command
{
    protected static $defaultName = 'app:exception:handled';

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

        return 0;
    }
}
