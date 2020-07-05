<?php


namespace Espo\Core\Upgrades\Actions\Upgrade;
class Install extends \Espo\Core\Upgrades\Actions\Base\Install
{
    protected function finalize()
    {
        $manifest = $this->getManifest();

        $this->getConfig()->set('version', $manifest['version']);
        $this->getConfig()->save();
    }

    /**
     * Delete temporary package files
     *
     * @return boolean
     */
    protected function deletePackageFiles()
    {
        $res = parent::deletePackageFiles();
        $res &= $this->deletePackageArchive();

        return $res;
    }
}
