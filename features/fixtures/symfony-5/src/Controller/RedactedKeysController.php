<?php

namespace App\Controller;

use Exception;
use Bugsnag\Client;
use Bugsnag\Report;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedactedKeysController
{
    /**
     * @Route("/redacted-keys")
     */
    public function __invoke(Client $bugsnag): Response
    {
        $bugsnag->notifyException(
            new Exception('This is a handled exception'),
            function (Report $report) {
                $report->addMetadata([
                    'testing' => [
                        'a' => 1,
                        'b' => 2,
                        'c' => 3,
                        'xyz' => 4,
                    ],
                ]);
            }
        );

        return new Response(__METHOD__);
    }
}
