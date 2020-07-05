<?php


namespace Espo\Core\FieldValidators;

class LinkType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        $idAttribute = $field . 'Id';

        if (!$entity->has($idAttribute)) {
            return false;
        }

        return $entity->get($idAttribute) !== null && $entity->get($idAttribute) !== '';
    }
}
