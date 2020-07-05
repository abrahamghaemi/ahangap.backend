<?php


namespace Espo\Core\Console\Commands;

class RunJob extends Base
{
    public function run($options, $flags, $argumentList)
    {
        $jobName = $options['job'] ?? null;
        $targetId = $options['targetId'] ?? null;
        $targetType = $options['targetType'] ?? null;

        if (!$jobName && count($argumentList)) {
            $jobName = $argumentList[0];
        }

        if (!$jobName) echo "No job specified.\n";

        $jobName = ucfirst(\Espo\Core\Utils\Util::hyphenToCamelCase($jobName));

        $container = $this->getContainer();
        $entityManager = $container->get('entityManager');

        $job = $entityManager->createEntity('Job', [
            'name' => $jobName,
            'job' => $jobName,
            'targetType' => $targetType,
            'targetId' => $targetId,
        ]);

        $cronManager = new \Espo\Core\CronManager($container);

        $result = $cronManager->runJob($job);

        if ($result) {
            echo "Job '{$jobName}' has been executed.\n";
        } else {
            echo "Job '{$jobName}' failed to execute.\n";
        }
    }
}
