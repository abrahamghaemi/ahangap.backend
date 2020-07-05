<?php


namespace Espo\Core\Utils\Database\Schema\rebuildActions;

class AddSystemUser extends \Espo\Core\Utils\Database\Schema\BaseRebuildActions
{
    public function afterRebuild()
    {
        $userId = $this->getConfig()->get('systemUserAttributes.id');
        $entity = $this->getEntityManager()->getEntity('User', $userId);
        if (!$entity) {
            $systemUserAttributes = $this->getConfig()->get('systemUserAttributes');
            $entity = $this->getEntityManager()->getEntity('User');
            $entity->set($systemUserAttributes);
            return $this->getEntityManager()->saveEntity($entity);
        }
    }
}
