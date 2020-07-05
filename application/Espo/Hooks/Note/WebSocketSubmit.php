<?php


namespace Espo\Hooks\Note;

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
        $parentId = $entity->get('parentId');
        $parentType = $entity->get('parentType');
        if (!$parentId) return;
        if (!$parentType) return;

        $data = (object) [
            'createdById' => $entity->get('createdById'),
        ];

        $topic = "streamUpdate.{$parentType}.{$parentId}";
        $this->getInjection('webSocketSubmission')->submit($topic, null, $data);
    }
}
