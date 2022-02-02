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
        $session = null;
        if ($this->request->hasPreviousSession()) {
            $session = $this->request->getSession();
        }

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
     * @return array
     */
    protected function getInput()
    {
        if ($this->isJsonContentType($this->request->headers->get('CONTENT_TYPE'))) {
            $parsed = json_decode($this->request->getContent(), true);

            if (is_array($parsed)) {
                return $parsed;
            }
        }

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

    /**
     * @param mixed $contentType
     *
     * @return bool
     */
    private function isJsonContentType($contentType)
    {
        if (is_string($contentType)) {
            return stripos($contentType, '/json') !== false
                || stripos($contentType, '+json') !== false;
        }

        return false;
    }
}
