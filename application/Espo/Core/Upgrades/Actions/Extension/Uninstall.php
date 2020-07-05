<?php


namespace Espo\Core\Upgrades\Actions\Extension;
use Espo\Core\Exceptions\Error;

class Uninstall extends \Espo\Core\Upgrades\Actions\Base\Uninstall
{
    protected $extensionEntity;

    /**
     * Get entity of this extension
     *
     * @return \Espo\Entities\Extension
     */
    protected function getExtensionEntity()
    {
        if (!isset($this->extensionEntity)) {
            $processId = $this->getProcessId();
            $this->extensionEntity = $this->getEntityManager()->getEntity('Extension', $processId);
            if (!isset($this->extensionEntity)) {
                throw new Error('Extension Entity not found.');
            }
        }

        return $this->extensionEntity;
    }

    protected function afterRunAction()
    {
        /** Set extension entity, isInstalled = false */
        $extensionEntity = $this->getExtensionEntity();

        $extensionEntity->set('isInstalled', false);
        $this->getEntityManager()->saveEntity($extensionEntity);
    }

    protected function getRestoreFileList()
    {
        if (!isset($this->data['restoreFileList'])) {
            $extensionEntity = $this->getExtensionEntity();
            $this->data['restoreFileList'] = $extensionEntity->get('fileList');
        }

        return $this->data['restoreFileList'];
    }
}
