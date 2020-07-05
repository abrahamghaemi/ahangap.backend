<?php


namespace Espo\Jobs;

use Espo\Core\Exceptions;

class CheckNewVersion extends \Espo\Core\Jobs\Base
{
    public function run()
    {
        if (!$this->getConfig()->get('adminNotifications') || !$this->getConfig()->get('adminNotificationsNewVersion')) {
            return true;
        }

        $job = $this->getEntityManager()->getEntity('Job');
        $job->set(array(
            'name' => 'Check for New Version (job)',
            'serviceName' => 'AdminNotifications',
            'methodName' => 'jobCheckNewVersion',
            'executeTime' => $this->getRunTime()
        ));

        $this->getEntityManager()->saveEntity($job);

        return true;
    }

    protected function getRunTime()
    {
        $hour = rand(0, 4);
        $minute = rand(0, 59);

        $nextDay = new \DateTime('+ 1 day');
        $time = $nextDay->format('Y-m-d') . ' ' . $hour . ':' . $minute . ':00';

        $timeZone = $this->getConfig()->get('timeZone');
        if (empty($timeZone)) {
            $timeZone = 'UTC';
        }

        $datetime = new \DateTime($time, new \DateTimeZone($timeZone));

        return $datetime->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }
}
