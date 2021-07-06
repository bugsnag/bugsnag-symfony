<?php

namespace App\Twig;

use Closure;
use LogicException;
use Bugsnag\Client;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class BugsnagExtension extends AbstractExtension
{
    private Client $bugsnag;

    public function __construct(Client $bugsnag)
    {
        $this->bugsnag = $bugsnag;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'trigger_unhandled_exception',
                Closure::fromCallable([$this, 'triggerUnhandledException'])
            ),
            new TwigFunction(
                'trigger_unhandled_error',
                Closure::fromCallable([$this, 'triggerUnhandledError'])
            ),
            new TwigFunction(
                'trigger_handled_exception',
                Closure::fromCallable([$this, 'triggerHandledException'])
            ),
            new TwigFunction(
                'trigger_handled_error',
                Closure::fromCallable([$this, 'triggerHandledError'])
            ),
        ];
    }

    private function triggerUnhandledException()
    {
        throw new LogicException('Crash!');
    }

    private function triggerUnhandledError()
    {
        abcxyz();
    }

    private function triggerHandledException()
    {
        $this->bugsnag->notifyException(new LogicException('Handled exception'));
    }

    private function triggerHandledError()
    {
        $this->bugsnag->notifyError('A handled error', 'handled');
    }
}
