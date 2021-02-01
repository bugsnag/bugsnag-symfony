<?php

namespace Bugsnag\BugsnagBundle\Tests\Listener;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Report;
use Exception;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface as Mock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
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
        /** @var Mock&Report $report */
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        /** @var Mock&Client $client */
        $client = Mockery::mock(Client::class);
        /** @var Mock&GetResponseForExceptionEvent $event */
        $event = Mockery::mock(GetResponseForExceptionEvent::class);

        $resolver = new SymfonyResolver();
        $exception = new Exception('oh no');

        $event->shouldReceive('getException')->once()->andReturn($exception);
        $report->shouldReceive('fromPHPThrowable')->once()->with('config', $exception)->andReturn($report);
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $report->shouldReceive('setMetaData')->once()->with([]);
        $client->shouldReceive('notify')->once()->with($report);

        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onKernelException($event);
    }

    public function testOnRequestArgumentException()
    {
        /** @var Mock&Client $client */
        $client = Mockery::mock(Client::class);

        $resolver = new SymfonyResolver();

        // PHPUnit 4 doesn't have 'expectException'
        if (method_exists(TestCase::class, 'expectException')) {
            $this->expectException(InvalidArgumentException::class);
        } else {
            $this->setExpectedException(InvalidArgumentException::class);
        }

        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onKernelRequest('This should throw an exception');
    }

    public function testOnConsoleError()
    {
        if (!class_exists(ConsoleErrorEvent::class)) {
            $this->markTestSkipped('ConsoleErrorEvent class not present');
        }

        /** @var Mock&Report $report */
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        /** @var Mock&Client $client */
        $client = Mockery::mock(Client::class);

        $resolver = new SymfonyResolver();
        $exception = new Exception('oh no');

        $event = new ConsoleErrorEvent(new StringInput(''), new NullOutput(), $exception, new Command('test'));
        $event->setExitCode(1);

        // Setup responses
        $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);
        $report->shouldReceive('fromPHPThrowable')->once()->with('config', $exception)->andReturn($report);
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleError($event);
    }

    public function testOnConsoleException()
    {
        if (!class_exists(ConsoleExceptionEvent::class)) {
            $this->markTestSkipped('ConsoleExceptionEvent class not present');
        }

        /** @var Mock&Report $report */
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        /** @var Mock&Client $client */
        $client = Mockery::mock(Client::class);

        $exception = new Exception('oh no');
        $resolver = new SymfonyResolver();
        $event = new ConsoleExceptionEvent(new Command('test'), new StringInput(''), new NullOutput(), $exception, 1);

        // Setup responses
        $report->shouldReceive('fromPHPThrowable')->once()->with('config', $exception)->andReturn($report);
        $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleException($event);
    }
}
