<?php

namespace AppBundle\Controller;

use RuntimeException;
use AppBundle\Exception\CustomException;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DiscardClassesController extends Controller
{
    /**
     * @Route("/discard-classes")
     */
    public function __invoke(): Response
    {
        $bugsnag = $this->get('bugsnag');
        $bugsnag->notifyException(new RuntimeException('This is a RuntimeException'));

        throw new CustomException('This is a CustomException');

        return new Response(__METHOD__);
    }
}
