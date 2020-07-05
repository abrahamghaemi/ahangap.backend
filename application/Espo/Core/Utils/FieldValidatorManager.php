<?php


namespace Espo\Core\Utils;

class FieldValidatorManager
{
    private $metadata;

    private $fieldManagerUtil;

    private $implHash = [];

    public function __construct(Metadata $metadata, FieldManagerUtil $fieldManagerUtil)
    {
        $this->metadata = $metadata;
        $this->fieldManagerUtil = $fieldManagerUtil;
    }

    public function check(\Espo\ORM\Entity $entity, string $field, string $type, $data = null) : bool
    {
        if (!$data) $data = (object) [];

        $fieldType = $this->fieldManagerUtil->getEntityTypeFieldParam($entity->getEntityType(), $field, 'type');

        $validationValue = $this->fieldManagerUtil->getEntityTypeFieldParam($entity->getEntityType(), $field, $type);

        $mandatoryValidationList = $this->metadata->get(['fields', $fieldType, 'mandatoryValidationList'], []);

        if (!in_array($type, $mandatoryValidationList)) {
            if (is_null($validationValue) || $validationValue === false) return true;
        }

        if (!array_key_exists($fieldType, $this->implHash)) {
            $this->loadImpl($fieldType);
        }
        $impl = $this->implHash[$fieldType];

        $methodName = 'check' . ucfirst($type);

        if (!method_exists($impl, $methodName)) return true;

        return $impl->$methodName($entity, $field, $validationValue, $data);
    }

    protected function loadImpl(string $fieldType)
    {
        $className = $this->metadata->get(['fields', $fieldType, 'validatorClassName']);

        if (!$className) {
            $className = '\\Espo\\Core\\FieldValidators\\' . ucfirst($fieldType) . 'Type';
            if (!class_exists($className)) {
                $className = '\\Espo\\Core\\FieldValidators\\BaseType';
            }
        }

        $this->implHash[$fieldType] = new $className($this->metadata, $this->fieldManagerUtil);
    }
}
