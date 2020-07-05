<?php


namespace Espo\Core\Utils;

class TemplateFileManager
{
    protected $config;

    protected $metadata;

    protected $fileManager;

    public function __construct(Config $config, Metadata $metadata, File\Manager $fileManager)
    {
        $this->config = $config;
        $this->metadata = $metadata;
        $this->fileManager = $fileManager;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    protected function getFileManager()
    {
        return $this->fileManager;
    }

    public function getTemplate($type, $name, $entityType = null, $defaultModuleName = null)
    {
        $fileName = $this->getTemplateFileName($type, $name, $entityType, $defaultModuleName);

        return file_get_contents($fileName);
    }

    public function saveTemplate($type, $name, $contents, $entityType = null)
    {
        $language = $this->getConfig()->get('language');
        if ($entityType) {
            $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
        } else {
            $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$name}.tpl";
        }

        $this->getFileManager()->putContents($fileName, $contents);
    }

    public function resetTemplate($type, $name, $entityType = null)
    {
        $language = $this->getConfig()->get('language');
        if ($entityType) {
            $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
        } else {
            $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$name}.tpl";
        }

        $this->getFileManager()->removeFile($fileName);
    }

    protected function getTemplateFileName($type, $name, $entityType = null, $defaultModuleName = null)
    {
        $language = $this->getConfig()->get('language');

        if ($entityType) {
            $moduleName = $this->getMetadata()->getScopeModuleName($entityType);

            $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
            if (file_exists($fileName)) return $fileName;

            if ($moduleName) {
                $fileName = "application/Espo/Modules/{$moduleName}/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
                if (file_exists($fileName)) return $fileName;
            }

            $fileName = "application/Espo/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
            if (file_exists($fileName)) return $fileName;
        }

        $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$name}.tpl";
        if (file_exists($fileName)) return $fileName;

        if ($defaultModuleName) {
            $fileName = "application/Espo/Modules/{$defaultModuleName}/Resources/templates/{$type}/{$language}/{$name}.tpl";
        } else {
            $fileName = "application/Espo/Resources/templates/{$type}/{$language}/{$name}.tpl";
        }
        if (file_exists($fileName)) return $fileName;

        $language = 'en_US';

        if ($entityType) {
            $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
            if (file_exists($fileName)) return $fileName;

            if ($moduleName) {
                $fileName = "application/Espo/Modules/{$moduleName}/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
                if (file_exists($fileName)) return $fileName;
            }

            $fileName = "application/Espo/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
            if (file_exists($fileName)) return $fileName;
        }

        $fileName = "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$name}.tpl";
        if (file_exists($fileName)) return $fileName;

        if ($defaultModuleName) {
            $fileName = "application/Espo/Modules/{$defaultModuleName}/Resources/templates/{$type}/{$language}/{$name}.tpl";
        } else {
            $fileName = "application/Espo/Resources/templates/{$type}/{$language}/{$name}.tpl";
        }

        return $fileName;
    }
}

