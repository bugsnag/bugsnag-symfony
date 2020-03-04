<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Throwable;

class DefaultErrorNotifyFilter implements ErrorNotifyFilter
{

    /**
     * @inheritDoc
     */
    public function shouldNotifyError(Throwable $throwable, array $meta)
    {
        return true;
    }

}
