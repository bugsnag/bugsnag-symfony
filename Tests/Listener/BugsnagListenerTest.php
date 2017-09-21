<?php

namespace Bugsnag\BugsnagBundle\Tests\Listener;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Report;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ReportStub
{
    const MIDDLEWARE_HANDLER = 'middleware_handler';
}

class BugsnagListenerTest extends TestCase
{
    use MockeryTrait;

    public function testOnKernelException()
    {
        // Create mocks
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        $client = Mockery::mock(Client::class);
        $event = Mockery::mock(GetResponseForExceptionEvent::class);
        $resolver = Mockery::mock(SymfonyResolver::class);

        // Setup responses
        $event->shouldReceive('getException')->once()->andReturn('exception');
        $report->shouldReceive('fromPHPThrowable')
            ->with('config', 'exception', true, ['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']])
            ->once()
            ->andReturn($report);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onKernelException($event);
    }

    public function testOnConsoleException()
    {
        // Create mocks
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        $client = Mockery::mock(Client::class);
        $event = Mockery::mock(ConsoleExceptionEvent::class);
        $resolver = Mockery::mock(SymfonyResolver::class);

        // Setup responses
        $event->shouldReceive('getException')->once()->andReturn('exception');
        $event->shouldReceive('getCommand')->once()->andReturn($event);
        $event->shouldReceive('getName')->once()->andReturn('test');
        $event->shouldReceive('getExitCode')->once()->andReturn(1);

        $report->shouldReceive('fromPHPThrowable')
            ->with('config', 'exception', 'middleware_handler', ['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']])
            ->once()
            ->andReturn($report);
        $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);

        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleException($event);
    }
}
