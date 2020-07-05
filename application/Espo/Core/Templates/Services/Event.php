<?php


namespace Espo\Core\Templates\Services;

use \Espo\ORM\Entity;

class Event extends \Espo\Services\Record
{
    protected $validateRequiredSkipFieldList = [
        'dateEnd'
    ];

    public function loadAdditionalFields(Entity $entity)
    {
        parent::loadAdditionalFields($entity);
        $this->loadRemindersField($entity);
    }

    protected function loadRemindersField(Entity $entity)
    {
        $reminders = $this->getRepository()->getEntityReminderList($entity);
        $entity->set('reminders', $reminders);
    }
}
