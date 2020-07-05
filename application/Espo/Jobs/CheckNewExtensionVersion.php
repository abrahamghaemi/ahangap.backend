<?php


namespace Espo\Jobs;

use Espo\Core\Exceptions;

class CheckNewExtensionVersion extends CheckNewVersion
{
    public function run()
    {
        if (!$this->getConfig()->get('adminNotifications') || !$this->getConfig()->get('adminNotificationsNewExtensionVersion')) {
            return true;
        }

        $job = $this->getEntityManager()->getEntity('Job');
        $job->set(array(
            'name' => 'Check for new versions of installed extensions (job)',
            'serviceName' => 'AdminNotifications',
            'methodName' => 'jobCheckNewExtensionVersion',
            'executeTime' => $this->getRunTime()
        ));

        $this->getEntityManager()->saveEntity($job);

        return true;
    }
}
