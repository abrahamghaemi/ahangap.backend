<?php


namespace Espo\Core\FieldValidators;

class EmailType extends BaseType
{
    public function checkRequired(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if ($this->isNotEmpty($entity, $field)) return true;

        $dataList = $entity->get($field . 'Data');
        if (!is_array($dataList)) return false;

        foreach ($dataList as $item) {
            if (!empty($item->emailAddress)) return true;
        }

        return false;
    }

    public function checkEmailAddress(\Espo\ORM\Entity $entity, string $field, $validationValue, $data) : bool
    {
        if ($this->isNotEmpty($entity, $field)) {
            $address = $entity->get($field);
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        $dataList = $entity->get($field . 'Data');
        if (is_array($dataList)) {
            foreach ($dataList as $item) {
                if (empty($item->emailAddress)) continue;
                $address = $item->emailAddress;
                if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        return $entity->has($field) && $entity->get($field) !== '' && $entity->get($field) !== null;
    }
}
