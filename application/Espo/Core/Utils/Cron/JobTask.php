<?php


namespace Espo\Core\Utils\Cron;

class JobTask extends \Spatie\Async\Task
{
    private $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function configure()
    {
    }

    public function run()
    {
        $app = new \Espo\Core\Application();
        try {
            $app->runJob($this->jobId);
        } catch (\Throwable $e) {
            $GLOBALS['log']->error("JobTask: Failed job run. Job id: ".$this->jobId.". Error details: ".$e->getMessage());
        }
    }
}
