<?php


namespace Espo\Core\FileStorage\Storages;

use \Espo\Core\Interfaces\Injectable;

abstract class Base implements Injectable
{
    protected $dependencyList = [];

    protected $injections = array();

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

    abstract public function hasDownloadUrl(\Espo\Entities\Attachment $attachment);

    abstract public function getDownloadUrl(\Espo\Entities\Attachment $attachment);

    abstract public function unlink(\Espo\Entities\Attachment $attachment);

    abstract public function getContents(\Espo\Entities\Attachment $attachment);

    abstract public function isFile(\Espo\Entities\Attachment $attachment);

    abstract public function putContents(\Espo\Entities\Attachment $attachment, $contents);

    abstract public function getLocalFilePath(\Espo\Entities\Attachment $attachment);
}
