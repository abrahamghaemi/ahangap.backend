<?php


namespace Espo\Core\FieldValidators;

class LinkParentType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        $idAttribute = $field . 'Id';
        $typeAttribute = $field . 'Type';

        if (!$entity->has($idAttribute) || $entity->get($idAttribute) === '' || $entity->get($idAttribute) === null) {
            return false;
        }

        if (!$entity->get($typeAttribute)) {
            return false;
        }

        return true;
    }
}
