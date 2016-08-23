<?php

namespace Bugsnag\BugsnagBundle\Request;

use Bugsnag\Request\NullRequest;
use Bugsnag\Request\ResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class SymfonyResolver implements ResolverInterface
{
    /**
     * The request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    protected $request;

    /**
     * Set the current request.
     *
     * @param \Symfony\Component\HttpFoundation\Request
     *
     * @return void
     */
    public function set(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Resolve the current request.
     *
     * @return \Bugsnag\Request\RequestInterface
     */
    public function resolve()
    {
        if (!$this->request) {
            return new NullRequest();
        }

        return new SymfonyRequest($this->request);
    }
}
