<?php


namespace Espo\Hooks\Notification;

use Espo\ORM\Entity;

class WebSocketSubmit extends \Espo\Core\Hooks\Base
{
    public static $order = 20;

    protected function init()
    {
        $this->addDependency('webSocketSubmission');
    }

    public function afterSave(Entity $entity, array $options = [])
    {
        if (!$this->getConfig()->get('useWebSocket')) return;
        if (!$entity->isNew()) return;
        $userId = $entity->get('userId');
        if (!$userId) return;

        $this->getInjection('webSocketSubmission')->submit('newNotification', $userId);
    }
}
