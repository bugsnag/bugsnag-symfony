<?php

namespace AppBundle\Command;

use LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ExceptionHandledCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:exception:handled');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bugsnag = $this->getContainer()->get('bugsnag');
        $bugsnag->notifyException(
            new LogicException('This is a handled exception in a command')
        );

        $output->writeln('Notfied of a handled exception!');

        return 0;
    }
}
