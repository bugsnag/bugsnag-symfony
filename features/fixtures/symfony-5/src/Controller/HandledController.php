<?php

namespace App\Controller;

use LogicException;
use Bugsnag\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HandledController
{
    /**
     * @Route("/handled/controller/exception")
     */
    public function handledExceptionInController(Client $bugsnag): Response
    {
        $bugsnag->notifyException(new LogicException('This is a handled exception'));

        return new Response(__METHOD__);
    }

    /**
     * @Route("/handled/controller/error")
     */
    public function handledErrorInController(Client $bugsnag): Response
    {
        $bugsnag->notifyError('Handled error', 'This is a handled error');

        return new Response(__METHOD__);
    }
}
