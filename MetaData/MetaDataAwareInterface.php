<?php

namespace Bugsnag\BugsnagBundle\MetaData;

interface MetaDataAwareInterface
{
    /**
     * Get meta data.
     *
     * @return array
     */
    public function getMetaData();
}
