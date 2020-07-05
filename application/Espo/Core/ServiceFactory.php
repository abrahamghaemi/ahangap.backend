<?php


namespace Espo\Core;
use \Espo\Core\Exceptions\Error;

use \Espo\Core\Utils\Util;

class ServiceFactory
{
    private $container;

    protected $cacheFile = 'data/cache/application/services.php';

    /**
     * @var array - path to Service files
     */
    protected $paths = [
        'corePath' => 'application/Espo/Services',
        'modulePath' => 'application/Espo/Modules/{*}/Services',
        'customPath' => 'custom/Espo/Custom/Services',
    ];

    protected $data;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getFileManager()
    {
        return $this->container->get('fileManager');
    }

    protected function getContainer()
    {
        return $this->container;
    }

    protected function init()
    {
        $classParser = $this->getContainer()->get('classParser');
        $classParser->setAllowedMethods(null);
        $this->data = $classParser->getData($this->paths, $this->cacheFile);
    }

    protected function getClassName($name)
    {
        if (!isset($this->data)) {
            $this->init();
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return false;
    }

    public function checkExists($name) {
        $className = $this->getClassName($name);
        if (!empty($className)) {
            return true;
        }
    }

    public function create($name)
    {
        $className = $this->getClassName($name);
        if (empty($className)) {
            throw new Error("Service '{$name}' was not found.");
        }
        return $this->createByClassName($className);
    }

    protected function createByClassName($className)
    {
        if (class_exists($className)) {
            $service = new $className();
            $dependencyList = $service->getDependencyList();
            foreach ($dependencyList as $name) {
                $service->inject($name, $this->container->get($name));
            }
            if (method_exists($service, 'prepare')) {
                $service->prepare();
            }
            return $service;
        }
        throw new Error("Class '$className' does not exist.");
    }
}
