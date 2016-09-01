<?php

namespace Bugsnag\BugsnagBundle\Tests\Request;

use Bugsnag\BugsnagBundle\Request\SymfonyRequest;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Request\NullRequest;
use Bugsnag\Request\RequestInterface;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

class SymfonyRequestTest extends TestCase
{
    use MockeryTrait;

    public function testCanResolveNullRequest()
    {
        $resolver = new SymfonyResolver();

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(NullRequest::class, $request);
    }

    public function testCanResolveSymfonyRequest()
    {
        $resolver = new SymfonyResolver();

        $resolver->set(new Request());

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(SymfonyRequest::class, $request);
    }
}
