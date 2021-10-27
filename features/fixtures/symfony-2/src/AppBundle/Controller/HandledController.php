<?php

namespace AppBundle\Controller;

use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HandledController extends Controller
{
    /**
     * @Route("/handled/controller/exception")
     */
    public function handledExceptionInController(): Response
    {
        $bugsnag = $this->get('bugsnag');
        $bugsnag->notifyException(new LogicException('This is a handled exception'));

        return new Response(__METHOD__);
    }

    /**
     * @Route("/handled/controller/error")
     */
    public function handledErrorInController(): Response
    {
        $bugsnag = $this->get('bugsnag');
        $bugsnag->notifyError('Handled error', 'This is a handled error');

        return new Response(__METHOD__);
    }

    /**
     * @Route("/handled/view/exception")
     */
    public function handledExceptionInView(): Response
    {
        return $this->render('handled/exception.html.twig');
    }

    /**
     * @Route("/handled/view/error")
     */
    public function handledErrorInView(): Response
    {
        return $this->render('handled/error.html.twig');
    }
}
