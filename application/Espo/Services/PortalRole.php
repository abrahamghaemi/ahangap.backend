<?php


namespace Espo\Services;

use \Espo\ORM\Entity;

class PortalRole extends Record
{
    protected function init()
    {
        parent::init();
        $this->addDependency('fileManager');
    }

    protected $forceSelectAllAttributes = true;

    public function afterCreateEntity(Entity $entity, $data)
    {
        parent::afterCreateEntity($entity, $data);
        $this->clearRolesCache();
    }

    public function afterUpdateEntity(Entity $entity, $data)
    {
        parent::afterUpdateEntity($entity, $data);
        $this->clearRolesCache();
    }

    protected function clearRolesCache()
    {
        $this->getInjection('fileManager')->removeInDir('data/cache/application/acl-portal');
    }
}

