<?php

namespace Bugsnag\BugsnagBundle\Tests\Request;

use Bugsnag\BugsnagBundle\Request\SilexRequest;
use Bugsnag\BugsnagBundle\Request\SilexResolver;
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
        $resolver = new SilexResolver();

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(NullRequest::class, $request);
    }

    public function testCanResolveSilexRequest()
    {
        $resolver = new SilexResolver();

        $resolver->set(new Request());

        $request = $resolver->resolve();

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertInstanceOf(SilexRequest::class, $request);
    }
}
