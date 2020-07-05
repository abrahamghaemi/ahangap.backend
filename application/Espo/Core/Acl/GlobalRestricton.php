<?php


namespace Espo\Core\Acl;

class GlobalRestricton
{
    protected $fieldTypeList = [
        'forbidden', // totally forbidden
        'internal', // reading forbidden, writing allowed
        'onlyAdmin', // forbidden for non admin users
        'readOnly', // read-only for all users
        'nonAdminReadOnly' // read-only for non-admin users
    ];

    protected $linkTypeList = [
        'forbidden', // totally forbidden
        'internal', // reading forbidden, writing allowed
        'onlyAdmin', // forbidden for non admin users
        'readOnly', // read-only for all users
        'nonAdminReadOnly' // read-only for non-admin users
    ];

    protected $cacheFilePath = 'data/cache/application/entityAcl.php';

    private $metadata;

    private $fileManager;

    private $fieldManagerUtil;

    private $data;

    public function __construct(
        \Espo\Core\Utils\Metadata $metadata,
        \Espo\Core\Utils\File\Manager $fileManager,
        \Espo\Core\Utils\FieldManagerUtil $fieldManagerUtil
    )
    {
        $this->metadata = $metadata;
        $this->fileManager = $fileManager;
        $this->fieldManagerUtil = $fieldManagerUtil;

        if (!file_exists($this->cacheFilePath)) {
            $this->buildCacheFile();
        }

        $this->data = include($this->cacheFilePath);
    }

    protected function buildCacheFile()
    {
        $scopeList = array_keys($this->getMetadata()->get(['entityDefs'], []));

        $data = (object) [];

        foreach ($scopeList as $scope) {
            $fieldList = array_keys($this->getMetadata()->get(['entityDefs', $scope, 'fields'], []));
            $linkList = array_keys($this->getMetadata()->get(['entityDefs', $scope, 'links'], []));

            $isNotEmpty = false;

            $scopeData = (object) [
                'fields' => (object) [],
                'attributes' => (object) [],
                'links' => (object) []
            ];

            foreach ($this->fieldTypeList as $type) {
                $resultFieldList = [];
                $resultAttributeList = [];

                foreach ($fieldList as $field) {
                    if ($this->getMetadata()->get(['entityAcl', $scope, 'fields', $field, $type])) {
                        $isNotEmpty = true;
                        $resultFieldList[] = $field;
                        $fieldAttributeList = $this->getFieldManagerUtil()->getAttributeList($scope, $field);
                        foreach ($fieldAttributeList as $attribute) {
                            $resultAttributeList[] = $attribute;
                        }
                    }
                }

                $scopeData->fields->$type = $resultFieldList;
                $scopeData->attributes->$type = $resultAttributeList;
            }
            foreach ($this->linkTypeList as $type) {
                $resultLinkList = [];
                foreach ($linkList as $link) {
                    if ($this->getMetadata()->get(['entityAcl', $scope, 'links', $link, $type])) {
                        $isNotEmpty = true;
                        $resultLinkList[] = $link;
                    }
                }
                $scopeData->links->$type = $resultLinkList;
            }

            if ($isNotEmpty) {
                $data->$scope = $scopeData;
            }
        }

        $this->data = $data;

        $this->getFileManager()->putPhpContents($this->cacheFilePath, $data, true);
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    protected function getFileManager()
    {
        return $this->fileManager;
    }

    protected function getFieldManagerUtil()
    {
        return $this->fieldManagerUtil;
    }

    public function getScopeRestrictedFieldList($scope, $type)
    {
        if (!property_exists($this->data, $scope)) return [];
        if (!property_exists($this->data->$scope, 'fields')) return [];
        if (!property_exists($this->data->$scope->fields, $type)) return [];

        return $this->data->$scope->fields->$type;
    }

    public function getScopeRestrictedAttributeList($scope, $type)
    {
        if (!property_exists($this->data, $scope)) return [];
        if (!property_exists($this->data->$scope, 'attributes')) return [];
        if (!property_exists($this->data->$scope->attributes, $type)) return [];

        return $this->data->$scope->attributes->$type;
    }

    public function getScopeRestrictedLinkList($scope, $type)
    {
        if (!property_exists($this->data, $scope)) return [];
        if (!property_exists($this->data->$scope, 'links')) return [];
        if (!property_exists($this->data->$scope->links, $type)) return [];

        return $this->data->$scope->links->$type;
    }
}
