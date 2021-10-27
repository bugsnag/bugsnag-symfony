<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ErrorHandledCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:error:handled');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bugsnag = $this->getContainer()->get('bugsnag');
        $bugsnag->notifyError(
            'Handled error',
            'This is a handled error in a command'
        );

        $output->writeln('Notfied of a handled error!');

        return 0;
    }
}
