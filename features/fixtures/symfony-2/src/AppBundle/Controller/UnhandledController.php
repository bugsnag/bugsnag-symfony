<?php

namespace AppBundle\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UnhandledController extends Controller
{
    /**
     * @Route("/unhandled/controller/exception")
     */
    public function unhandledExceptionInController(): Response
    {
        throw new RuntimeException('Crashing exception!');
    }

    /**
     * @Route("/unhandled/controller/error")
     */
    public function unhandledErrorInController(): Response
    {
        foo();
    }

    /**
     * @Route("/unhandled/view/exception")
     */
    public function unhandledExceptionInView(): Response
    {
        return $this->render('unhandled/exception.html.twig');
    }

    /**
     * @Route("/unhandled/view/error")
     */
    public function unhandledErrorInView(): Response
    {
        return $this->render('unhandled/error.html.twig');
    }
}
