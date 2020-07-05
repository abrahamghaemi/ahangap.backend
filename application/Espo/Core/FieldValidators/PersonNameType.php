<?php


namespace Espo\Core\FieldValidators;

class PersonNameType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        $isEmpty = true;
        foreach ($this->getActualAttributeList($entity, $field) as $attribute) {
            if ($attribute === 'salutation' . ucfirst($field)) {
                continue;
            }
            if ($entity->has($attribute) && $entity->get($attribute) !== '') {
                $isEmpty = false;
                break;
            }
        }
        if ($isEmpty) return false;
        return true;
    }
}
