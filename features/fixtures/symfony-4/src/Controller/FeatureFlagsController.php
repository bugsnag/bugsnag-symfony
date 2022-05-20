<?php

namespace App\Controller;

use Bugsnag\Client;
use Bugsnag\Report;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FeatureFlagsController extends AbstractController
{
    /**
     * @Route("/feature-flags/unhandled")
     */
    public function unhandled(Request $request, Client $bugsnag): Response
    {
        if ($request->query->has('clear_all_flags')) {
            $bugsnag->registerCallback(function (Report $report) {
                $report->clearFeatureFlags();
            });
        }

        throw new RuntimeException('Crashing exception!');
    }

    /**
     * @Route("/feature-flags/handled")
     */
    public function handled(Request $request, Client $bugsnag): Response
    {
        $bugsnag->notifyException(
            new RuntimeException('This is a handled exception'),
            function (Report $report) use ($request) {
                $report->clearFeatureFlag('from global on error 2');
                $report->addFeatureFlag('from notify on error', 'notify 7636390');

                if ($request->query->has('clear_all_flags')) {
                    $report->clearFeatureFlags();
                }
            }
        );
    }
}
