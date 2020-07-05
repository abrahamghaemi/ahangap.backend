<?php


namespace Espo\Core\FileStorage;

use \Espo\Entities\Attachment;

use \Espo\Core\Exceptions\Error;

class Manager
{
    private $implementations = array();

    private $implementationClassNameMap = array();

    private $container;

    public function __construct(array $implementationClassNameMap, $container)
    {
        $this->implementationClassNameMap = $implementationClassNameMap;
        $this->container = $container;
    }

    private function getImplementation($storage = null)
    {
        if (!$storage) {
            $storage = 'EspoUploadDir';
        }

        if (array_key_exists($storage, $this->implementations)) {
            return $this->implementations[$storage];
        }

        if (!array_key_exists($storage, $this->implementationClassNameMap)) {
            throw new Error("FileStorageManager: Unknown storage '{$storage}'");
        }
        $className = $this->implementationClassNameMap[$storage];

        $implementation = new $className();
        foreach ($implementation->getDependencyList() as $dependencyName) {
            $implementation->inject($dependencyName, $this->container->get($dependencyName));
        }
        $this->implementations[$storage] = $implementation;

        return $implementation;
    }

    public function isFile(Attachment $attachment)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->isFile($attachment);
    }

    public function getContents(Attachment $attachment)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->getContents($attachment);
    }

    public function putContents(Attachment $attachment, $contents)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->putContents($attachment, $contents);
    }

    public function unlink(Attachment $attachment)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->unlink($attachment);
    }

    public function getLocalFilePath(Attachment $attachment)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->getLocalFilePath($attachment);
    }

    public function hasDownloadUrl(Attachment $attachment)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->hasDownloadUrl($attachment);
    }

    public function getDownloadUrl(Attachment $attachment)
    {
        $implementation = $this->getImplementation($attachment->get('storage'));
        return $implementation->getDownloadUrl($attachment);
    }
}
