<?php

namespace App\Controller;

use RuntimeException;
use Bugsnag\Client;
use App\Exception\CustomException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscardClassesController
{
    #[Route('/discard-classes')]
    public function __invoke(Client $bugsnag): Response
    {
        $bugsnag->notifyException(new RuntimeException('This is a RuntimeException'));

        throw new CustomException('This is a CustomException');

        return new Response(__METHOD__);
    }
}
