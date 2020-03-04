<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Throwable;

interface ErrorNotifyFilter
{

    /**
     * Allow filtering some Exception to fit the strategy needed for each project.
     * Return <code>true</code> to notify the Exception, <code>false</code> to filter it out.
     *
     * @param Throwable $throwable
     * @param array $meta
     *
     * @return boolean
     */
    public function shouldNotifyError(Throwable $throwable, array $meta);

}
