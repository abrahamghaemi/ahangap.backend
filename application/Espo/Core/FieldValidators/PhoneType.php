<?php


namespace Espo\Core\FieldValidators;

class PhoneType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if ($this->isNotEmpty($entity, $field)) return true;

        $dataList = $entity->get($field . 'Data');
        if (!is_array($dataList)) return false;

        foreach ($dataList as $item) {
            if (!empty($item->phoneNumber)) return true;
        }

        return false;
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        return $entity->has($field) && $entity->get($field) !== '' && $entity->get($field) !== null;
    }
}
