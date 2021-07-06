<?php

namespace App\Controller;

use Exception;
use LogicException;
use Bugsnag\Client;
use Bugsnag\Report;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RedactedKeysController
{
    /**
     * @Route("/redacted-keys")
     */
    public function __invoke(Request $request, Client $bugsnag): Response
    {
        $redactedKeys = $request->query->get('keys');

        if (is_array($redactedKeys)) {
            $bugsnag->setRedactedKeys($redactedKeys);
        }

        $bugsnag->notifyException(
            new Exception('This is a handled exception'),
            function (Report $report) use ($redactedKeys) {
                $report->addMetadata([
                    'testing' => [
                        'a' => 1,
                        'b' => 2,
                        'c' => 3,
                        'xyz' => 4,
                        // add the keys we got for debugging purposes
                        'redactedKeys' => $redactedKeys,
                    ],
                ]);
            }
        );

        return new Response(__METHOD__);
    }
}
