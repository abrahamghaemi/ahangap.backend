<?php


namespace Espo\Core\FieldValidators;

class ArrayType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    public function checkMaxCount(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if (!$this->isNotEmpty($entity, $field)) return true;
        $list = $entity->get($field);
        if (count($list) > $validationValue) return false;
        return true;
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        if (!$entity->has($field) || $entity->get($field) === null) return false;
        $list = $entity->get($field);
        if (!is_array($list)) return false;
        if (count($list)) return true;
        return false;
    }
}
