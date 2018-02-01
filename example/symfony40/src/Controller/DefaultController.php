<?php

namespace App\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return new Response('Welcome to the Bugsnag Symfony 4 example. Visit the
         file "src/Controller/DefaultController" to see how certain functions 
        are implemented, and routes they can be tested on.');
    }

    /**
     * @Route("/crash/", name="crash")
     */
    public function crash()
    {
        throw new RuntimeException('It crashed!  Go to your Bugsnag dashboard to view the exception');
    }

    /**
     * @Route("/callback/", name="callback")
     */
    public function callback()
    {
        $this->container->get('bugsnag')->registerCallback(function ($report) {
            $report->setMetaData([
                'account' => [
                    'name' => 'Acme Co.',
                    'paying_customer' => true,
                ],
            ]);
        });

        throw new RuntimeException('It crashed!  Go to your Bugsnag dashboard to view the exception and metadata');
    }

    /**
     * @Route("/notify/", name="notify")
     */
    public function notify()
    {
        $this->container->get('bugsnag')->notifyException(new RuntimeException("It didn't crash!"));

        return new Response("It didn't crash, but check your Bugsnag dashboard for the manual notification");
    }

    /**
     * @Route("/metadata/", name="metadata")
     */
    public function metadata()
    {
        $this->container->get('bugsnag')->notifyException(new RuntimeException("It didn't crash, with metadata!"), function ($report) {
            $report->setMetaData([
                'diagnostics' => [
                    'error' => 'RuntimeException',
                    'state' => 'Caught',
                ],
            ]);
        });

        return new Response("It didn't crash, but check your Bugsnag dashboard for the manual notification with additional metadata");
    }

    /**
     * @Route("/severity/", name="severity")
     */
    public function severity()
    {
        $this->container->get('bugsnag')->notifyException(new RuntimeException("It didn't crash, with severity!"), function ($report) {
            $report->setSeverity('info');
        });

        return new Response("It didn't crash, but check your Bugsnag dashboard for the manual notification, and check the severity of the report");
    }
}
