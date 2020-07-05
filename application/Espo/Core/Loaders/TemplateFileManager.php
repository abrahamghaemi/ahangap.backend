<?php


namespace Espo\Core\Loaders;

class TemplateFileManager extends Base
{
    public function load()
    {
        $templateFileManager = new \Espo\Core\Utils\TemplateFileManager(
            $this->getContainer()->get('config'),
            $this->getContainer()->get('metadata'),
            $this->getContainer()->get('fileManager')
        );

        return $templateFileManager;
    }
}

