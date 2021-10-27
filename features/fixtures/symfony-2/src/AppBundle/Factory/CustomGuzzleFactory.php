<?php

namespace AppBundle\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;

class CustomGuzzleFactory
{
    public static function create(): Client
    {
        $handler = HandlerStack::create();
        $handler->push(Middleware::mapRequest(function ($request) {
            return $request->withHeader('X-Custom-Guzzle', 'yes');
        }));

        return new Client(['handler' => $handler]);
    }
}
