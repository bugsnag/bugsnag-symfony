<?php

namespace Bugsnag\BugsnagBundle\Tests\Listener;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\MetaData\MetaDataAwareInterface;
use Bugsnag\BugsnagBundle\MetaData\MetaDataAwareTrait;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Report;
use Exception;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ReportStub
{
    const MIDDLEWARE_HANDLER = 'middleware_handler';
}

class MetaDataExceptionStub extends Exception implements MetaDataAwareInterface
{
    use MetaDataAwareTrait;
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
            ->with('config', 'exception')
            ->once()
            ->andReturn($report);
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $report->shouldReceive('setMetaData')->once()->with([]);
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onKernelException($event);
    }

    public function testOnConsoleError()
    {
        if (!class_exists('Symfony\Component\Console\Event\ConsoleErrorEvent')) {
            $this->markTestSkipped('ConsoleErrorEvent class not present - pre-Symfony3.3');
        } else {
            // Create mocks
            $report = Mockery::namedMock(Report::class, ReportStub::class);
            $client = Mockery::mock(Client::class);
            $input = Mockery::mock(InputInterface::class);
            $output = Mockery::mock(OutputInterface::class);
            $command = Mockery::mock(Command::class);
            $event = new ConsoleErrorEvent($input, $output, new Exception(), $command); // Unable to mock as final
            $event->setExitCode(1);
            $resolver = Mockery::mock(SymfonyResolver::class);

            // Setup responses
            $command->shouldReceive('getName')->once()->andReturn('test');
            $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);
            $report->shouldNotReceive('addMetaData');
            $report->shouldReceive('fromPHPThrowable')
                ->with('config', 'exception')
                ->once()
                ->andReturn($report);
            $report->shouldReceive('setUnhandled')->once()->with(true);
            $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
            $client->shouldReceive('getConfig')->once()->andReturn('config');
            $client->shouldReceive('notify')->once()->with($report);

            // Initiate test
            $listener = new BugsnagListener($client, $resolver, true);
            $listener->onConsoleError($event);
        }
    }

    public function testOnConsoleErrorWithMetaData()
    {
        if (!class_exists('Symfony\Component\Console\Event\ConsoleErrorEvent')) {
            $this->markTestSkipped('ConsoleErrorEvent class not present - pre-Symfony3.3');
        } else {
            // Create mocks
            $report = Mockery::namedMock(Report::class, ReportStub::class);
            $client = Mockery::mock(Client::class);
            $input = Mockery::mock(InputInterface::class);
            $output = Mockery::mock(OutputInterface::class);
            $command = Mockery::mock(Command::class);
            $event = new ConsoleErrorEvent($input, $output, $exception = new MetaDataExceptionStub(), $command); // Unable to mock as final
            $event->setExitCode(1);
            $resolver = Mockery::mock(SymfonyResolver::class);

            $exception->addMetaData(['myTab' => ['foo' => 'bar']]);
            $exception->addMetaData(['myTab' => ['foo' => 'test', 'bazz' => 'Bugsnag']]); // Test replacement

            // Setup responses
            $command->shouldReceive('getName')->once()->andReturn('test');
            $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);
            $report->shouldReceive('addMetaData')->once()->with(['myTab' => ['foo' => ['bar', 'test'], 'bazz' => 'Bugsnag']]);
            $report->shouldReceive('fromPHPThrowable')
                ->with('config', 'exception')
                ->once()
                ->andReturn($report);
            $report->shouldReceive('setUnhandled')->once()->with(true);
            $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
            $client->shouldReceive('getConfig')->once()->andReturn('config');
            $client->shouldReceive('notify')->once()->with($report);

            // Initiate test
            $listener = new BugsnagListener($client, $resolver, true);
            $listener->onConsoleError($event);
        }
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
        $event->shouldReceive('getCommand')->twice()->andReturn($event);
        $event->shouldReceive('getName')->once()->andReturn('test');
        $event->shouldReceive('getExitCode')->once()->andReturn(1);

        $report->shouldReceive('fromPHPThrowable')
            ->with('config', 'exception')
            ->once()
            ->andReturn($report);
        $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);
        $report->shouldNotReceive('addMetaData');
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleException($event);
    }

    public function testOnConsoleExceptionWithMetaData()
    {
        // Create mocks
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        $client = Mockery::mock(Client::class);
        $event = Mockery::mock(ConsoleExceptionEvent::class);
        $resolver = Mockery::mock(SymfonyResolver::class);

        $exception = new MetaDataExceptionStub();
        $exception->addMetaData(['myTab' => ['foo' => 'bar']]);
        $exception->addMetaData(['myTab' => ['foo' => 'test', 'bazz' => 'Bugsnag']]); // Test replacement

        // Setup responses
        $event->shouldReceive('getException')->once()->andReturn($exception);
        $event->shouldReceive('getCommand')->twice()->andReturn($event);
        $event->shouldReceive('getName')->once()->andReturn('test');
        $event->shouldReceive('getExitCode')->once()->andReturn(1);

        $report->shouldReceive('fromPHPThrowable')
            ->with('config', 'exception')
            ->once()
            ->andReturn($report);
        $report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);
        $report->shouldReceive('addMetaData')->once()->with(['myTab' => ['foo' => ['bar', 'test'], 'bazz' => 'Bugsnag']]);
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleException($event);
    }
}
