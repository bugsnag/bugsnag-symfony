<?php

namespace AppBundle\Controller;

use Bugsnag\Report;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FeatureFlagsController extends Controller
{
    /**
     * @Route("/feature-flags/unhandled")
     */
    public function unhandled(Request $request): Response
    {
        $bugsnag = $this->get('bugsnag');

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
    public function handled(Request $request): Response
    {
        $bugsnag = $this->get('bugsnag');

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
