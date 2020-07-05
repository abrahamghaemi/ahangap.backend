<?php


namespace Espo\Core\FieldValidators;

class VarcharType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    public function checkMaxLength(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if ($this->isNotEmpty($entity, $field)) {
            $value = $entity->get($field);
            if (mb_strlen($value) > $validationValue) {
                return false;
            }
        }
        return true;
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        return $entity->has($field) && $entity->get($field) !== '' && $entity->get($field) !== null;
    }
}
