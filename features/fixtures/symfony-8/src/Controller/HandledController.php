<?php

namespace App\Controller;

use LogicException;
use Bugsnag\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HandledController extends AbstractController
{
    #[Route('/handled/controller/exception', name: 'handled_controller_exception')]
    public function handledExceptionInController(Client $bugsnag): Response
    {
        $bugsnag->notifyException(new LogicException('This is a handled exception'));

        return new Response(__METHOD__);
    }

    #[Route('/handled/controller/error', name: 'handled_controller_error')]
    public function handledErrorInController(Client $bugsnag): Response
    {
        $bugsnag->notifyError('Handled error', 'This is a handled error');

        return new Response(__METHOD__);
    }

    #[Route('/handled/view/exception', name: 'handled_view_exception')]
    public function handledExceptionInView(): Response
    {
        return $this->render('handled/exception.html.twig');
    }

    #[Route('/handled/view/error', name: 'handled_view_error')]
    public function handledErrorInView(): Response
    {
        return $this->render('handled/error.html.twig');
    }
}
