<?php

namespace Bugsnag\BugsnagBundle\Tests\Listener;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
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
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class ReportStub
{
    const MIDDLEWARE_HANDLER = 'middleware_handler';
}

class BugsnagListenerTest extends TestCase
{
    use MockeryTrait;

    /**
     * @var BugsnagListener
     */
    private $listener;

    /**
     * @var Mockery\Mock
     */
    private $report;
    /**
     * @var Mockery\Mock
     */
    private $client;

    public function setUp()
    {
        parent::setUp();

        // Setup a default mock report
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        $report->shouldReceive('fromPHPThrowable')->byDefault()
            ->with('config', 'exception')
            ->andReturn($report);
        $report->shouldReceive('setUnhandled')->once()->with(true)->byDefault();
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']])->byDefault();
        $report->shouldReceive('setMetaData')->once()->with([])->byDefault();
        $this->report = $report;

        // Setup a default mock client
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('getConfig')->once()->andReturn('config')->byDefault();
        $client->shouldReceive('notify')->once()->with($this->report)->byDefault();
        $this->client = $client;

        // Instantiate a fresh listener to test
        $this->listener = new BugsnagListener($this->client, Mockery::mock(SymfonyResolver::class), true);
    }

    public function testOnKernelException()
    {
        // mock event
        $event = Mockery::mock(GetResponseForExceptionEvent::class);
        $event->shouldReceive('getException')->once()->andReturn('exception');

        // Initiate test
        $this->listener->onKernelException($event);
    }

    public function testOnConsoleError()
    {
        if (!class_exists('Symfony\Component\Console\Event\ConsoleErrorEvent')) {
            $this->markTestSkipped('ConsoleErrorEvent class not present - pre-Symfony3.3');
        } else {
            // Create mocks
            $input = Mockery::mock(InputInterface::class);
            $output = Mockery::mock(OutputInterface::class);
            $command = Mockery::mock(Command::class);
            $event = new ConsoleErrorEvent($input, $output, new Exception(), $command); // Unable to mock as final
            $event->setExitCode(1);

            // Setup responses
            $command->shouldReceive('getName')->once()->andReturn('test');
            $this->report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);

            // Initiate test
            $this->listener->onConsoleError($event);
        }
    }

    public function testOnConsoleException()
    {
        // mock event
        $event = Mockery::mock(ConsoleExceptionEvent::class);
        $event->shouldReceive('getException')->once()->andReturn('exception');
        $event->shouldReceive('getCommand')->twice()->andReturn($event);
        $event->shouldReceive('getName')->once()->andReturn('test');
        $event->shouldReceive('getExitCode')->once()->andReturn(1);

        // assert meta data
        $this->report->shouldReceive('setMetaData')->once()->with(['command' => ['name' => 'test', 'status' => 1]]);

        // Initiate test
        $this->listener->onConsoleException($event);
    }

    public function testOnWorkerFailedDoesNotCallNotifyIfRetry()
    {
        // mock an event triggered via a message that can be retried
        $event = Mockery::mock(WorkerMessageFailedEvent::class);
        $event->shouldReceive('willRetry')->once()->andReturn('true');

        // sendNotify is not called, so these expectations need to be reset
        $this->report->shouldNotReceive('setUnhandled','setSeverityReason', 'setMetaData');
        $this->client->shouldNotReceive('notify', 'getConfig');

        $this->listener->onWorkerMessageFailed($event);
    }

    public function testOnWorkerFailedDoesCallNotify()
    {
        // mock an event triggered via a message that can be retried
        $event = Mockery::mock(WorkerMessageFailedEvent::class);
        $event->shouldReceive('willRetry')->once()->andReturn(false);
        $event->shouldReceive('getThrowable')->once()->andReturn(new \Exception());

        $this->listener->onWorkerMessageFailed($event);
    }
}
