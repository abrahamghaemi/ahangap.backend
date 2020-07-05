<?php


namespace Espo\Core\Hooks;

use Espo\Core\Interfaces\Injectable;

abstract class Base implements Injectable
{
    protected $injections = [];

    public static $order = 9;

    protected $dependencyList = [
        'container',
        'entityManager',
        'config',
        'metadata',
        'aclManager',
        'user',
    ];

    protected $dependencies = []; // for backward compatibility

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
    }

    public function getDependencyList()
    {
        return array_merge($this->dependencyList, $this->dependencies);
    }

    protected function addDependencyList(array $list)
    {
        foreach ($list as $item) {
            $this->addDependency($item);
        }
    }

    protected function addDependency($name)
    {
        $this->dependencyList[] = $name;
    }

    protected function getInjection($name)
    {
        return $this->injections[$name];
    }

    public function inject($name, $object)
    {
        $this->injections[$name] = $object;
    }

    protected function getContainer()
    {
        return $this->getInjection('container');
    }

    protected function getEntityManager()
    {
        return $this->getInjection('entityManager');
    }

    protected function getUser()
    {
        return $this->getInjection('user');
    }

    protected function getAcl()
    {
        return $this->getContainer()->get('acl');
    }

    protected function getAclManager()
    {
        return $this->getInjection('aclManager');
    }

    protected function getConfig()
    {
        return $this->getInjection('config');
    }

    protected function getMetadata()
    {
        return $this->getInjection('metadata');
    }
}
