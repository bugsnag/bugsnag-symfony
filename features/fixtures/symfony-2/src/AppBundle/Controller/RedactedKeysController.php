<?php

namespace AppBundle\Controller;

use Exception;
use Bugsnag\Report;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RedactedKeysController extends Controller
{
    /**
     * @Route("/redacted-keys")
     */
    public function __invoke(): Response
    {
        $bugsnag = $this->get('bugsnag');
        $bugsnag->notifyException(
            new Exception('This is a handled exception'),
            function (Report $report) {
                $report->addMetadata([
                    'testing' => [
                        'a' => 1,
                        'b' => 2,
                        'c' => 3,
                        'xyz' => 4,
                        'password' => 'passw0rd1',
                        'a_passWORD_confirmation' => 'passw0rd2',
                        'cookie' => 'thanks',
                        'authorization' => 'user:pass',
                        'php-AUTH-user' => 'user',
                        'php-auth-pw' => 'pass',
                        'PHP-auth-DIGEST' => 'ives',
                    ],
                ]);
            }
        );

        return new Response(__METHOD__);
    }
}
