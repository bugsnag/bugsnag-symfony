<?php

namespace Bugsnag\BugsnagBundle\Tests\Request;

use Bugsnag\BugsnagBundle\Request\SymfonyRequest;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Request\NullRequest;
use Bugsnag\Request\RequestInterface;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use PHPUnit\Framework\MockObject\MockObject;
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
        /** @var MockObject&Request $symfonyRequest */
        $symfonyRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['hasPreviousSession', 'getSession'])
            ->getMock();

        $resolver = new SymfonyResolver();
        $resolver->set($symfonyRequest);

        $request = $resolver->resolve();

        $symfonyRequest->expects($this->once())
            ->method('hasPreviousSession')
            ->willReturn(false);

        $symfonyRequest->expects($this->never())
            ->method('getSession');

        $session = $request->getSession();

        $this->assertSame([], $session);
    }

    public function testResolveSessionWhenPreviousSessionExists()
    {
        /** @var MockObject&Request $symfonyRequest */
        $symfonyRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['hasPreviousSession', 'getSession'])
            ->getMock();

        /** @var MockObject&Session $symfonySession */
        $symfonySession = $this->getMockBuilder(Session::class)
            ->setMethods(['all'])
            ->getMock();

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

        $this->assertSame(['foobar' => 'baz'], $session);
    }
}
