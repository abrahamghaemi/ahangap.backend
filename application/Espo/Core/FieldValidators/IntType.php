<?php


namespace Espo\Core\FieldValidators;

class IntType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    public function checkMax(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if (!$this->isNotEmpty($entity, $field)) return true;
        if ($entity->get($field) > $validationValue) return false;
        return true;
    }

    public function checkMin(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if (!$this->isNotEmpty($entity, $field)) return true;
        if ($entity->get($field) < $validationValue) return false;
        return true;
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        return $entity->has($field) && $entity->get($field) !== null;
    }
}
