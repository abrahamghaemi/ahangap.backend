<?php


namespace Espo\Core\Utils\Autoload;

class Loader
{
    private $config;

    private $fileManager;

    private $namespaceLoader;

    public function __construct(\Espo\Core\Utils\Config $config, \Espo\Core\Utils\File\Manager $fileManager)
    {
        $this->config = $config;
        $this->fileManager = $fileManager;
        $this->namespaceLoader = new NamespaceLoader($config, $fileManager);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getFileManager()
    {
        return $this->fileManager;
    }

    public function getNamespaceLoader()
    {
        return $this->namespaceLoader;
    }

    public function register(array $autoloadList)
    {
        /* load "psr-4", "psr-0", "classmap" */
        $this->getNamespaceLoader()->register($autoloadList);

        /* load "autoloadFileList" */
        $this->registerAutoloadFileList($autoloadList);

        /* load "files" */
        $this->registerFiles($autoloadList);
    }

    protected function registerAutoloadFileList(array $autoloadList)
    {
        $keyName = 'autoloadFileList';

        if (!isset($autoloadList[$keyName])) return;

        foreach ($autoloadList[$keyName] as $filePath) {
            if (file_exists($filePath)) {
                require_once($filePath);
            }
        }
    }

    protected function registerFiles(array $autoloadList)
    {
        $keyName = 'files';

        if (!isset($autoloadList[$keyName])) return;

        foreach ($autoloadList[$keyName] as $id => $filePath) {
            if (file_exists($filePath)) {
                require_once($filePath);
            }
        }
    }
}
