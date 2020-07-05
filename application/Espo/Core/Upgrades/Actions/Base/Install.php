<?php


namespace Espo\Core\Upgrades\Actions\Base;
use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Util;

class Install extends \Espo\Core\Upgrades\Actions\Base
{
    /**
     * Main installation process
     *
     * @param  string $processId Upgrade/Extension ID, gotten in upload stage
     * @return bool
     */
    public function run($data)
    {
        $processId = $data['id'];

        $GLOBALS['log']->debug('Installation process ['.$processId.']: start run.');

        if (empty($processId)) {
            throw new Error('Installation package ID was not specified.');
        }

        $this->setProcessId($processId);

        $this->initialize();

        /** check if an archive is unzipped, if no then unzip */
        $packagePath = $this->getPackagePath();
        if (!file_exists($packagePath)) {
            $this->unzipArchive();
            $this->isAcceptable();
        }

        //check permissions copied and deleted files
        $this->checkIsWritable();

        $this->enableMaintenanceMode();

        $this->beforeRunAction();

        $this->backupExistingFiles();

        //beforeInstallFiles
        if (!$this->copyFiles('before')) {
            $this->throwErrorAndRemovePackage('Cannot copy beforeInstall files.');
        }

        /* run before install script */
        if (!isset($data['skipBeforeScript']) || !$data['skipBeforeScript']) {
            $this->runScript('before');
        }

        /* remove files defined in a manifest "deleteBeforeCopy" */
        $this->deleteFiles('deleteBeforeCopy', true);

        /* copy files from directory "Files" to EspoCRM files */
        if (!$this->copyFiles()) {
            $this->throwErrorAndRemovePackage('Cannot copy files.');
        }

        /* remove files defined in a manifest */
        $this->deleteFiles('delete', true);

        $this->deleteFiles('vendor');
        $this->copyFiles('vendor');

        $this->disableMaintenanceMode();

        if (!isset($data['skipSystemRebuild']) || !$data['skipSystemRebuild']) {
            if (!$this->systemRebuild()) {
                $this->throwErrorAndRemovePackage('Error occurred while EspoCRM rebuild.');
            }
        }

        //afterInstallFiles
        if (!$this->copyFiles('after')) {
            $this->throwErrorAndRemovePackage('Cannot copy afterInstall files.');
        }

        /* run after install script */
        if (!isset($data['skipAfterScript']) || !$data['skipAfterScript']) {
            $this->runScript('after');
        }

        $this->afterRunAction();

        $this->finalize();

        /* delete unziped files */
        $this->deletePackageFiles();

        if ($this->getManifestParam('skipBackup')) {
            $this->getFileManager()->removeInDir([$this->getPath('backupPath'), self::FILES]);
        }

        $GLOBALS['log']->debug('Installation process ['.$processId.']: end run.');

        $this->clearCache();
    }

    protected function restoreFiles()
    {
        $GLOBALS['log']->info('Installer: Restore previous files.');

        $backupPath = $this->getPath('backupPath');
        $backupFilePath = Util::concatPath($backupPath, self::FILES);

        $backupFileList = $this->getRestoreFileList();
        $copyFileList = $this->getCopyFileList();
        $deleteFileList = array_diff($copyFileList, $backupFileList);

        $res = $this->copy($backupFilePath, '', true);
        if (!empty($deleteFileList)) {
            $res &= $this->getFileManager()->remove($deleteFileList, null, true);
        }

        if ($res) {
            $this->getFileManager()->removeInDir($backupPath, true);
        }

        return $res;
    }

    protected function throwErrorAndRemovePackage($errorMessage = '')
    {
        $this->restoreFiles();
        parent::throwErrorAndRemovePackage($errorMessage);
    }
}
