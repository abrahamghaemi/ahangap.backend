<?php


namespace Espo\Jobs;

use \Espo\Core\Exceptions;

class ProcessJobQueueE0 extends \Espo\Core\Jobs\Base
{
    public function run()
    {
        $limit = $this->getConfig()->get('jobE0MaxPortion', 100);

        $cronManager = new \Espo\Core\CronManager($this->getContainer());

        $cronManager->processPendingJobs('e0', $limit, true, true);
    }
}
