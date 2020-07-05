<?php


namespace Espo\Core\Templates\Services;

use \Espo\ORM\Entity;

class Company extends \Espo\Services\Record
{
    protected function getDuplicateWhereClause(Entity $entity, $data)
    {
        return array(
            'name' => $entity->get('name')
        );
    }
}
