<?php


namespace Espo\Core;

use \Espo\Core\Exceptions\Error;

class InjectableFactory
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createByClassName($className)
    {
        if (class_exists($className)) {
            $service = new $className();
            if (!($service instanceof \Espo\Core\Interfaces\Injectable)) {
                throw new Error("Class '$className' is not instance of Injectable interface");
            }
            $dependencyList = $service->getDependencyList();
            foreach ($dependencyList as $name) {
                $service->inject($name, $this->container->get($name));
            }
            if (method_exists($service, 'prepare')) {
                $service->prepare();
            }
            return $service;
        }
        throw new Error("Class '$className' does not exist");
    }

    protected function getMetadata()
    {
        return $this->getContainer()->get('metadata');
    }

    protected function getContainer()
    {
        return $this->container;
    }
}
