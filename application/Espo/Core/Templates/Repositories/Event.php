<?php


namespace Espo\Core\Templates\Repositories;

class Event extends \Espo\Core\Repositories\Event
{
    protected function beforeSave(\Espo\ORM\Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);
    }
}
