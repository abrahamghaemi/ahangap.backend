<?php


namespace Espo\Core\Utils;

class FieldManagerUtil
{
    private $metadata;

    private $fieldByTypeListCache = [];

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    private function getAttributeListByType($scope, $name, $type)
    {
        $fieldType = $this->getMetadata()->get('entityDefs.' . $scope . '.fields.' . $name . '.type');
        if (!$fieldType) return [];

        $defs = $this->getMetadata()->get('fields.' . $fieldType);
        if (!$defs) return [];
        if (is_object($defs)) {
            $defs = get_object_vars($defs);
        }

        $fieldList = [];

        if (isset($defs[$type . 'Fields'])) {
            $list = $defs[$type . 'Fields'];
            $naming = 'suffix';
            if (isset($defs['naming'])) {
                $naming = $defs['naming'];
            }
            if ($naming == 'prefix') {
                foreach ($list as $f) {
                    if ($f === '') {
                        $fieldList[] = $name;
                    } else {
                        $fieldList[] = $f . ucfirst($name);
                    }
                }
            } else {
                foreach ($list as $f) {
                    $fieldList[] = $name . ucfirst($f);
                }
            }
        } else {
            if ($type == 'actual') {
                $fieldList[] = $name;
            }
        }

        return $fieldList;
    }

    public function getAdditionalActualAttributeList($scope, $name)
    {
        $attributeList = [];

        $list = $this->getMetadata()->get(['entityDefs', $scope, 'fields', $name, 'additionalAttributeList']);
        if (empty($list)) return [];

        $type = $this->getMetadata()->get(['entityDefs', $scope, 'fields', $name, 'type']);
        if (!$type) return [];

        $naming = $this->getMetadata()->get(['fields', $type, 'naming'], 'suffix');

        if ($naming == 'prefix') {
            foreach ($list as $f) {
                $attributeList[] = $f . ucfirst($name);
            }
        } else {
            foreach ($list as $f) {
                $attributeList[] = $name . ucfirst($f);
            }
        }

        return $attributeList;

    }

    public function getActualAttributeList($scope, $name)
    {
        return array_merge($this->getAttributeListByType($scope, $name, 'actual'), $this->getAdditionalActualAttributeList($scope, $name));
    }

    public function getNotActualAttributeList($scope, $name)
    {
        return $this->getAttributeListByType($scope, $name, 'notActual');
    }

    public function getAttributeList($scope, $name)
    {
        return array_merge($this->getActualAttributeList($scope, $name), $this->getNotActualAttributeList($scope, $name));
    }

    public function getFieldByTypeList($scope, $type)
    {
        if (!array_key_exists($scope, $this->fieldByTypeListCache)) {
            $this->fieldByTypeListCache[$scope] = [];
        }

        if (!array_key_exists($type, $this->fieldByTypeListCache[$scope])) {
            $fieldDefs = $this->getMetadata()->get(['entityDefs', $scope, 'fields'], []);
            $list = [];
            foreach ($fieldDefs as $field => $defs) {
                if (isset($defs['type']) && $defs['type'] === $type) {
                    $list[] = $field;
                }
            }
            $this->fieldByTypeListCache[$scope][$type] = $list;
        }

        return $this->fieldByTypeListCache[$scope][$type];
    }

    private function getFieldTypeAttributeListByType($fieldType, $name, $type)
    {
        $defs = $this->getMetadata()->get(['fields', $fieldType]);
        if (!$defs) return [];

        $attributeList = [];

        if (isset($defs[$type . 'Fields'])) {
            $list = $defs[$type . 'Fields'];
            $naming = 'suffix';
            if (isset($defs['naming'])) {
                $naming = $defs['naming'];
            }
            if ($naming == 'prefix') {
                foreach ($list as $f) {
                    $attributeList[] = $f . ucfirst($name);
                }
            } else {
                foreach ($list as $f) {
                    $attributeList[] = $name . ucfirst($f);
                }
            }
        } else {
            if ($type == 'actual') {
                $attributeList[] = $name;
            }
        }

        return $attributeList;
    }

    public function getFieldTypeAttributeList($fieldType, $name)
    {
        return array_merge(
            $this->getFieldTypeAttributeListByType($fieldType, $name, 'actual'),
            $this->getFieldTypeAttributeListByType($fieldType, $name, 'notActual')
        );
    }

    public function getEntityTypeFieldList($entityType)
    {
        return array_keys($this->getMetadata()->get(['entityDefs', $entityType, 'fields'], []));
    }

    public function getEntityTypeFieldParam($entityType, $field, $param)
    {
        return $this->getMetadata()->get(['entityDefs', $entityType, 'fields', $field, $param]);
    }
}
