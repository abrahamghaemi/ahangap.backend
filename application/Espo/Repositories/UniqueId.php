<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;

class UniqueId extends \Espo\Core\ORM\Repositories\RDB
{
    protected $hooksDisabled = true;

    protected $processFieldsAfterSaveDisabled = true;

    protected $processFieldsBeforeSaveDisabled = true;

    protected $processFieldsAfterRemoveDisabled = true;

    public function getNew() : ?Entity
    {
        $entity = parent::getNew();
        $entity->set('name', \Espo\Core\Utils\Util::generateId());
        return $entity;
    }
}
