<?php


namespace Espo\Core\Services;

use \Espo\Core\Interfaces\Injectable;

abstract class Base implements Injectable
{
    protected $dependencyList = [
        'config',
        'entityManager',
        'user',
        'serviceFactory',
    ];

    protected $injections = [];

    public function inject($name, $object)
    {
        $this->injections[$name] = $object;
    }

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
    }

    public function prepare()
    {
    }

    protected function getInjection($name)
    {
        return $this->injections[$name];
    }

    protected function addDependency($name)
    {
        $this->dependencyList[] = $name;
    }

    protected function addDependencyList(array $list)
    {
        foreach ($list as $item) {
            $this->addDependency($item);
        }
    }

    public function getDependencyList()
    {
        return $this->dependencyList;
    }

    protected function getEntityManager()
    {
        return $this->getInjection('entityManager');
    }

    protected function getConfig()
    {
        return $this->getInjection('config');
    }

    protected function getUser()
    {
        return $this->getInjection('user');
    }

    protected function getServiceFactory()
    {
        return $this->getInjection('serviceFactory');
    }
}
