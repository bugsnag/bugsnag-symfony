<?php

namespace AppBundle\Bugsnag;

use Bugsnag\Report;

class MetadataCallback
{
    /**
     * @param \Bugsnag\Report $report
     *
     * @return void
     */
    public function registerCallback(Report $report)
    {
        $report->setMetaData([
            'custom_data_1' => 'custom_value_1',
            'custom_data_2' => 'custom_value_2',
        ]);
    }
}
