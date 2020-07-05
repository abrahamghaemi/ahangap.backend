<?php


namespace Espo\Core\FieldValidators;

use \Espo\ORM\Entity;

class BaseType
{
    private $metadata;
    private $fieldManagerUtil;

    public function __construct(\Espo\Core\Utils\Metadata $metadata, \Espo\Core\Utils\FieldManagerUtil $fieldManagerUtil)
    {
        $this->metadata = $metadata;
        $this->fieldManagerUtil = $fieldManagerUtil;
    }

    protected function getActualAttributeList(Entity $entity, string $field) : array
    {
        return $this->getFieldManagerUtil()->getActualAttributeList($entity->getEntityType(), $field);
    }

    protected function getMetadata() : \Espo\Core\Utils\Metadata
    {
        return $this->metadata;
    }

    protected function getFieldManagerUtil() : \Espo\Core\Utils\FieldManagerUtil
    {
        return $this->fieldManagerUtil;
    }
}
