<?php


namespace Espo\Jobs;

use \Espo\Core\Exceptions;

class ProcessJobQueueQ0 extends \Espo\Core\Jobs\Base
{
    public function run()
    {
        $limit = $this->getConfig()->get('jobQ1MaxPortion', 200);

        $cronManager = new \Espo\Core\CronManager($this->getContainer());

        $cronManager->processPendingJobs('q0', $limit, true, true);
    }
}
