<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OomController
{
    /**
     * @Route("/oom/big")
     */
    public function bigOom(): Response
    {
        $a = str_repeat('a', 2147483647);

        return $this->noOomResponse();
    }

    /**
     * A response with memory debug information for use when we didn't go OOM
     */
    private function noOomResponse(): Response
    {
        $limit = ini_get('memory_limit');
        $memory = var_export(memory_get_usage(), true);
        $peak = var_export(memory_get_peak_usage(), true);

        return new Response("No OOM!\n{$limit}\n{$memory}\n{$peak}");
    }
}
