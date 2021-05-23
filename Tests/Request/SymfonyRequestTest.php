<?php

namespace Bugsnag\BugsnagBundle\Tests\Request;

use Bugsnag\BugsnagBundle\Request\SymfonyRequest;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Request\NullRequest;
use Bugsnag\Request\RequestInterface;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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

    public function testResolveSessionWhenPreviousSessionDoesNotExists()
    {
        $symfonyRequest = $this->getMock(Request::class);

        $resolver = new SymfonyResolver();
        $resolver->set($symfonyRequest);

        $request = $resolver->resolve();

        $symfonyRequest->expects($this->once())
            ->method('hasPreviousSession')
            ->willReturn(false);

        $symfonyRequest->expects($this->never())
            ->method('getSession');

        $session = $request->getSession();
        $this->assertTrue(is_array($session));
        $this->assertEmpty($session);
    }

    public function testResolveSessionWhenPreviousSessionExists()
    {
        $symfonyRequest = $this->getMock(Request::class);
        $symfonySession = $this->getMock(Session::class);

        $resolver = new SymfonyResolver();
        $resolver->set($symfonyRequest);

        $request = $resolver->resolve();

        $symfonyRequest->expects($this->once())
            ->method('hasPreviousSession')
            ->willReturn(true);

        $symfonyRequest->expects($this->once())
            ->method('getSession')
            ->willReturn($symfonySession);

        $symfonySession->expects($this->once())
            ->method('all')
            ->willReturn(['foobar' => 'baz']);

        $session = $request->getSession();
        $this->assertTrue(is_array($session));
        $this->assertEquals(['foobar' => 'baz'], $session);
    }
}
