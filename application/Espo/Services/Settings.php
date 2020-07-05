<?php


namespace Espo\Services;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\NotFound;

use Espo\ORM\Entity;

class Settings extends \Espo\Core\Services\Base
{
    protected function init()
    {
        parent::init();
        $this->addDependency('fieldManagerUtil');
        $this->addDependency('metadata');
        $this->addDependency('acl');
        $this->addDependency('container');
    }

    protected function getFieldManagerUtil()
    {
        return $this->getInjection('fieldManagerUtil');
    }

    protected function getMetadata()
    {
        return $this->getInjection('metadata');
    }

    protected function getAcl()
    {
        return $this->getInjection('acl');
    }

    protected function getContainer()
    {
        return $this->getInjection('container');
    }

    public function getConfigData()
    {
        $data = $this->getConfig()->getAllData();

        $ignoreItemList = [];

        foreach ($this->getSystemOnlyItemList() as $item) {
            $ignoreItemList[] = $item;
        }

        if (!$this->getUser()->isAdmin() || $this->getUser()->isSystem()) {
            foreach ($this->getAdminOnlyItemList() as $item) {
                $ignoreItemList[] = $item;
            }
        }

        if ($this->getUser()->isSystem()) {
            foreach ($this->getUserOnlyItemList() as $item) {
                $ignoreItemList[] = $item;
            }
        }

        if ($this->getConfig()->get('restrictedMode') && !$this->getUser()->isSuperAdmin()) {
            foreach ($this->getConfig()->getSuperAdminOnlySystemItemList() as $item) {
                $ignoreItemList[] = $item;
            }
        }

        if ($portal = $this->getContainer()->get('portal')) {
            $this->getContainer()->get('entityManager')->getRepository('Portal')->loadUrlField($portal);
            $data->siteUrl = $portal->get('url');
        }

        foreach ($ignoreItemList as $item) {
            unset($data->$item);
        }

        $fieldDefs = $this->getMetadata()->get(['entityDefs', 'Settings', 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if ($fieldParams['type'] === 'password') {
                unset($data->$field);
            }
        }

        $this->filterData($data);

        return $data;
    }

    public function setConfigData($data)
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }

        $ignoreItemList = [];

        foreach ($this->getSystemOnlyItemList() as $item) {
            $ignoreItemList[] = $item;
        }

        if ($this->getConfig()->get('restrictedMode') && !$this->getUser()->isSuperAdmin()) {
            foreach ($this->getConfig()->getSuperAdminOnlyItemList() as $item) {
                $ignoreItemList[] = $item;
            }
            foreach ($this->getConfig()->getSuperAdminOnlySystemItemList() as $item) {
                $ignoreItemList[] = $item;
            }
        }

        foreach ($ignoreItemList as $item) {
            unset($data->$item);
        }

        if (
            (isset($data->useCache) && $data->useCache !== $this->getConfig()->get('useCache'))
            ||
            (isset($data->aclStrictMode) && $data->aclStrictMode !== $this->getConfig()->get('aclStrictMode'))
        ) {
            $this->getContainer()->get('dataManager')->clearCache();
        }

        $this->getConfig()->setData($data);

        $result = $this->getConfig()->save();

        if ($result === false) {
            throw new Error('Cannot save settings');
        }

        if (isset($data->defaultCurrency) || isset($data->baseCurrency) || isset($data->currencyRates)) {
            $this->getContainer()->get('dataManager')->rebuildDatabase([]);
        }

        return $result;
    }

    protected function filterData($data)
    {
        if ($this->getUser()->isSystem()) return;

        if ($this->getUser()->isAdmin()) return;

        if (
            !$this->getAcl()->checkScope('Email', 'create')
            ||
            !$this->getConfig()->get('outboundEmailIsShared')
        ) {
            unset($data->outboundEmailFromAddress);
            unset($data->outboundEmailFromName);
            unset($data->outboundEmailBccAddress);
        }
    }

    public function getAdminOnlyItemList()
    {
        $itemList = $this->getConfig()->getAdminOnlyItemList();

        $fieldDefs = $this->getMetadata()->get(['entityDefs', 'Settings', 'fields']);
        foreach ($fieldDefs as $field => $fieldParams) {
            if (!empty($fieldParams['onlyAdmin'])) {
                foreach ($this->getFieldManagerUtil()->getAttributeList('Settings', $field) as $attribute) {
                    $itemList[] = $attribute;
                }
            }
        }

        return $itemList;
    }

    public function getUserOnlyItemList()
    {
        $itemList = $this->getConfig()->getUserOnlyItemList();

        $fieldDefs = $this->getMetadata()->get(['entityDefs', 'Settings', 'fields']);
        foreach ($fieldDefs as $field => $fieldParams) {
            if (!empty($fieldParams['onlyUser'])) {
                foreach ($this->getFieldManagerUtil()->getAttributeList('Settings', $field) as $attribute) {
                    $itemList[] = $attribute;
                }
            }
        }

        return $itemList;
    }

    public function getSystemOnlyItemList()
    {
        $itemList = $this->getConfig()->getSystemOnlyItemList();

        $fieldDefs = $this->getMetadata()->get(['entityDefs', 'Settings', 'fields']);
        foreach ($fieldDefs as $field => $fieldParams) {
            if (!empty($fieldParams['onlySystem'])) {
                foreach ($this->getFieldManagerUtil()->getAttributeList('Settings', $field) as $attribute) {
                    $itemList[] = $attribute;
                }
            }
        }

        return $itemList;
    }

}
