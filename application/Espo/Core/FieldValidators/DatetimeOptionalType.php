<?php


namespace Espo\Core\FieldValidators;

class DatetimeOptionalType extends DatetimeType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        if ($entity->has($field) && $entity->get($field) !== null) return true;
        if ($entity->has($field . 'Date') && $entity->get($field . 'Date') !== null) return true;
        return false;
    }
}
