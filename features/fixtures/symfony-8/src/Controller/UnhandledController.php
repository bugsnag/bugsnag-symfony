<?php

namespace App\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UnhandledController extends AbstractController
{
    #[Route('/unhandled/controller/exception', name: 'unhandled_controller_exception')]
    public function unhandledExceptionInController(): Response
    {
        throw new RuntimeException('Crashing exception!');
    }

    #[Route('/unhandled/controller/error', name: 'unhandled_controller_error')]
    public function unhandledErrorInController(): Response
    {
        foo();
    }

    #[Route('/unhandled/view/exception', name: 'unhandled_view_exception')]
    public function unhandledExceptionInView(): Response
    {
        return $this->render('unhandled/exception.html.twig');
    }

    #[Route('/unhandled/view/error', name: 'unhandled_view_error')]
    public function unhandledErrorInView(): Response
    {
        return $this->render('unhandled/error.html.twig');
    }
}
