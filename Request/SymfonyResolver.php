<?php

namespace Bugsnag\BugsnagBundle\Request;

use Bugsnag\Request\NullRequest;
use Bugsnag\Request\ResolverInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class SymfonyResolver implements ResolverInterface
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Create a new symfony request resolver instance.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Resolve the current request.
     *
     * @return \Bugsnag\Request\RequestInterface
     */
    public function resolve()
    {
        if ($this->app->runningInConsole()) {
            return new NullRequest();
        }

        $request = $this->app->make(Request::class);

        return new SymfonyRequest($request);
    }
}
