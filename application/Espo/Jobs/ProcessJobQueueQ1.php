<?php


namespace Espo\Jobs;

use \Espo\Core\Exceptions;

class ProcessJobQueueQ1 extends \Espo\Core\Jobs\Base
{
    public function run()
    {
        $limit = $this->getConfig()->get('jobQ1MaxPortion', 500);

        $cronManager = new \Espo\Core\CronManager($this->getContainer());

        $cronManager->processPendingJobs('q1', $limit, true, true);
    }
}
