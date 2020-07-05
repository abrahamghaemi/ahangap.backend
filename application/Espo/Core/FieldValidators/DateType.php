<?php


namespace Espo\Core\FieldValidators;

class DateType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        return $entity->has($field) && $entity->get($field) !== null;
    }
}
