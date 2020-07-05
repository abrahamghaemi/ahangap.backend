<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;

class ExternalAccount extends \Espo\Core\ORM\Repositories\RDB
{
    public function get($id = null) : ?Entity
    {
        $entity = parent::get($id);
        if (empty($entity) && !empty($id)) {
            $entity = $this->get();
            $entity->id = $id;
        }
        return $entity;
    }
}
