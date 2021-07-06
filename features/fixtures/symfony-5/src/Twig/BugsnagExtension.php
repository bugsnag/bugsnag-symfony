<?php

namespace App\Twig;

use Closure;
use LogicException;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class BugsnagExtension extends AbstractExtension
{
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
}
