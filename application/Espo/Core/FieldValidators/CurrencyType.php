<?php


namespace Espo\Core\FieldValidators;

class CurrencyType extends FloatType
{
    protected function isNotEmpty(\Espo\ORM\Entity $entity, $field)
    {
        return
            $entity->has($field) && $entity->get($field) !== null &&
            $entity->has($field . 'Currency') && $entity->get($field . 'Currency') !== null &&
            $entity->get($field . 'Currency') !== '';
    }
}
