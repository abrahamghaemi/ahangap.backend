<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;

class StreamNotesAcl extends \Espo\Core\Hooks\Base
{
    protected $noteService = null;

    public static $order = 10;

    protected function init()
    {
        parent::init();
        $this->addDependency('serviceFactory');
        $this->addDependency('aclManager');
    }

    protected function getServiceFactory()
    {
        return $this->getInjection('serviceFactory');
    }

    protected function getAclManager()
    {
        return $this->getInjection('aclManager');
    }

    public function afterSave(Entity $entity, array $options = [])
    {
        if (!empty($options['noStream'])) return;
        if (!empty($options['silent'])) return;
        if (!empty($options['skipStreamNotesAcl'])) return;

        if ($entity->isNew()) return;

        if (!$this->noteService) {
            $this->noteService = $this->getServiceFactory()->create('Note');
        }

        $forceProcessNoteNotifications = !empty($options['forceProcessNoteNotifications']);

        $this->noteService->processNoteAcl($entity, $forceProcessNoteNotifications);
    }
}
