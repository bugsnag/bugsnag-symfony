<?php

namespace App\Controller;

use stdClass;
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
     * @Route("/oom/small")
     */
    public function smallOom(): Response
    {
        ini_set('memory_limit', memory_get_usage() + (1024 * 1024 * 4));
        ini_set('display_errors', true);

        $i = 0;

        gc_disable();

        while ($i++ < 12345678) {
            $a = new stdClass;
            $a->b = $a;
        }

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

        return new Response(
            <<<HTML
                No OOM!
                {$limit}
                {$memory}
                {$peak}
            HTML
        );
    }
}
