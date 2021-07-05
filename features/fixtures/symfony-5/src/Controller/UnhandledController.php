<?php

namespace App\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UnhandledController
{
    /**
     * @Route("/unhandled/controller/exception")
     */
    public function unhandledExceptionInController(): Response
    {
        throw new RuntimeException('Crashing exception!');
    }
}
