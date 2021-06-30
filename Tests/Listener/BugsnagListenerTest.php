<?php

namespace Bugsnag\BugsnagBundle\Tests\Listener;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Configuration;
use Bugsnag\Report;
use Exception;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface as Mock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Debug\Exception\OutOfMemoryException as OutOfMemorySymfony2Or3;
use Symfony\Component\ErrorHandler\Error\OutOfMemoryError as OutOfMemorySymfony4Plus;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class ReportStub
{
    const MIDDLEWARE_HANDLER = 'middleware_handler';
}

class BugsnagListenerTest extends TestCase
{
    use MockeryTrait;

    /**
     * @runInSeparateProcess the Mockery Report mock breaks any test using the real Report class
     */
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
        $report->shouldReceive('setSeverity')->once()->with('error');
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $report->shouldReceive('setMetaData')->once()->with([]);
        $client->shouldReceive('notify')->once()->with($report);

        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onKernelException($event);
    }

    /**
     * @runInSeparateProcess the Mockery Report mock breaks any test using the real Report class
     */
    public function testOnKernelExceptionWithAnOom()
    {
        $file = __FILE__;
        $line = __LINE__;
        $oomMessage = 'Allowed memory size of 12345 bytes exhausted (tried to allocate 9876 bytes)';

        if (class_exists(OutOfMemorySymfony4Plus::class)) {
            $error = [
                'type' => E_ERROR,
                'message' => $oomMessage,
                'file' => $file,
                'line' => $line,
            ];

            $oom = new OutOfMemorySymfony4Plus($oomMessage, 1, $error);
        } else {
            $oom = new OutOfMemorySymfony2Or3($oomMessage, 1, E_ERROR, $file, $line);
        }

        /** @var Mock&Report $report */
        $report = Mockery::namedMock(Report::class, ReportStub::class);
        /** @var Mock&Client $client */
        $client = Mockery::mock(Client::class);
        /** @var Mock&GetResponseForExceptionEvent $event */
        $event = Mockery::mock(GetResponseForExceptionEvent::class);

        $resolver = new SymfonyResolver();

        $event->shouldReceive('getException')->once()->andReturn($oom);

        $report->shouldReceive('fromPHPThrowable')->once()->with('config', $oom)->andReturn($report);
        $report->shouldReceive('setUnhandled')->once()->with(true);
        $report->shouldReceive('setSeverity')->once()->with('error');
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $report->shouldReceive('setMetaData')->once()->with([]);

        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('getMemoryLimitIncrease')->twice()->andReturn(1234567890);
        $client->shouldReceive('notify')->once()->with($report);

        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onKernelException($event);
    }

    /**
     * @runInSeparateProcess the Mockery Report mock breaks any test using the real Report class
     */
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

    /**
     * @runInSeparateProcess the Mockery Report mock breaks any test using the real Report class
     */
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
        $report->shouldReceive('setSeverity')->once()->with('error');
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleError($event);
    }

    /**
     * @runInSeparateProcess the Mockery Report mock breaks any test using the real Report class
     */
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
        $report->shouldReceive('setSeverity')->once()->with('error');
        $report->shouldReceive('setSeverityReason')->once()->with(['type' => 'unhandledExceptionMiddleware', 'attributes' => ['framework' => 'Symfony']]);
        $client->shouldReceive('getConfig')->once()->andReturn('config');
        $client->shouldReceive('notify')->once()->with($report);

        // Initiate test
        $listener = new BugsnagListener($client, $resolver, true);
        $listener->onConsoleException($event);
    }

    public function testReportIsCreatedWhenAWorkerMessageFailedEventFires()
    {
        if (!class_exists(WorkerMessageFailedEvent::class)) {
            $this->markTestSkipped('This test requires symfony/messenger');
        }

        $exception = new RuntimeException('Failed to do stuff');
        $config = new Configuration('api key');

        /** @var MockObject&Client $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods()
            ->getMock();

        $client->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (Report $report) use ($exception) {
                $this->assertSame($exception->getMessage(), $report->getMessage());
                $this->assertTrue($report->getUnhandled());
                $this->assertSame('error', $report->getSeverity());
                $this->assertSame(
                    [
                        'type' => 'unhandledExceptionMiddleware',
                        'attributes' => ['framework' => 'Symfony'],
                    ],
                    $report->getSeverityReason()
                );

                $this->assertSame(
                    ['Messenger' => ['willRetry' => false]],
                    $report->getMetaData()
                );

                return true;
            }));

        $client->expects($this->once())->method('getConfig')->willReturn($config);
        $client->expects($this->once())->method('flush');

        $listener = new BugsnagListener($client, new SymfonyResolver(), true);

        $message = new stdClass();
        $envelope = new Envelope($message);

        $event = new WorkerMessageFailedEvent($envelope, 'name', $exception);

        $listener->onWorkerMessageFailed($event);
    }

    public function testWillRetryIsAttachedAsMetadataWhenWorkerMessageFails()
    {
        if (!class_exists(WorkerMessageFailedEvent::class)) {
            $this->markTestSkipped('This test requires symfony/messenger');
        }

        $exception = new RuntimeException('Failed to do stuff');
        $config = new Configuration('api key');

        /** @var MockObject&Client $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods()
            ->getMock();

        $client->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (Report $report) use ($exception) {
                $this->assertSame($exception->getMessage(), $report->getMessage());
                $this->assertTrue($report->getUnhandled());
                $this->assertSame('error', $report->getSeverity());
                $this->assertSame(
                    [
                        'type' => 'unhandledExceptionMiddleware',
                        'attributes' => ['framework' => 'Symfony'],
                    ],
                    $report->getSeverityReason()
                );

                $this->assertSame(
                    ['Messenger' => ['willRetry' => true]],
                    $report->getMetaData()
                );

                return true;
            }));

        $client->expects($this->once())->method('getConfig')->willReturn($config);
        $client->expects($this->once())->method('flush');

        $listener = new BugsnagListener($client, new SymfonyResolver(), true);

        $message = new stdClass();
        $envelope = new Envelope($message);

        $event = new WorkerMessageFailedEvent($envelope, 'name', $exception);
        $event->setForRetry();

        $listener->onWorkerMessageFailed($event);
    }

    public function testItShouldFlushWhenAWorkerMessageHandledEventFires()
    {
        if (!class_exists(WorkerMessageFailedEvent::class)) {
            $this->markTestSkipped('This test requires symfony/messenger');
        }

        $config = new Configuration('api key');

        /** @var MockObject&Client $client */
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods()
            ->getMock();

        $client->expects($this->once())->method('flush');

        $listener = new BugsnagListener($client, new SymfonyResolver(), true);

        $message = new stdClass();
        $envelope = new Envelope($message);

        $event = new WorkerMessageHandledEvent($envelope, 'name');

        $listener->onWorkerMessageHandled($event);
    }
}
