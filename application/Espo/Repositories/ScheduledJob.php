<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;

class ScheduledJob extends \Espo\Core\ORM\Repositories\RDB
{
    protected $hooksDisabled = true;

    protected $processFieldsAfterSaveDisabled = true;

    protected $processFieldsBeforeSaveDisabled = true;

    protected $processFieldsAfterRemoveDisabled = true;


    protected function afterSave(Entity $entity, array $options = array())
    {
        parent::afterSave($entity, $options);

        if ($entity->isAttributeChanged('scheduling')) {
            $jobList = $this->getEntityManager()->getRepository('Job')->where([
                'scheduledJobId' => $entity->id,
                'status' => \Espo\Core\CronManager::PENDING
            ])->find();

            foreach ($jobList as $job) {
                $this->getEntityManager()->removeEntity($job);
            }
        }
    }
}
