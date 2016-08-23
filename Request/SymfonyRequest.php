<?php

namespace Bugsnag\BugsnagBundle\Request;

use Bugsnag\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class SymfonyRequest implements RequestInterface
{
    /**
     * The symfony request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * Create a new symfony request instance.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Are we currently processing a request?
     *
     * @return bool
     */
    public function isRequest()
    {
        return true;
    }

    /**
     * Get the session data.
     *
     * @return array
     */
    public function getSession()
    {
        $session = $this->request->getSession();

        return $session ? $session->all() : [];
    }

    /**
     * Get the cookies.
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->request->cookies->all();
    }

    /**
     * Get the request formatted as meta data.
     *
     * @return array
     */
    public function getMetaData()
    {
        $data = [];

        $data['url'] = $this->request->getUri();

        $data['httpMethod'] = $this->request->getMethod();

        $data['params'] = $this->getInput() + $this->request->query->all();

        $data['clientIp'] = $this->request->getClientIp();

        if ($agent = $this->request->headers->get('User-Agent')) {
            $data['userAgent'] = $agent;
        }

        if ($headers = $this->request->headers->all()) {
            $data['headers'] = $headers;
        }

        return ['request' => $data];
    }

    /**
     * Get the input source for the request.
     *
     * This is based on Laravel's input source generation.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInput()
    {
        $type = $this->request->headers->get('CONTENT_TYPE');

        // If it's json, decode it
        if (stripos($type, '/json') !== false || stripos($type, '+json') !== false) {
            return json_decode($this->request->getContent(), true);
        }

        // Yes, we really do want request->request
        return $this->request->request->all();
    }

    /**
     * Get the request context.
     *
     * @return string|null
     */
    public function getContext()
    {
        return $this->request->getMethod().' '.$this->request->getPathInfo();
    }

    /**
     * Get the request user id.
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->request->getClientIp();
    }
}
