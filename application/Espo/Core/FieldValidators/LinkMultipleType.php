<?php


namespace Espo\Core\FieldValidators;

class LinkMultipleType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        return count($entity->getLinkMultipleIdList($field)) > 0;
    }
}
