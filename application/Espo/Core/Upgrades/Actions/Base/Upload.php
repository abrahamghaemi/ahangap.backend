<?php


namespace Espo\Core\Upgrades\Actions\Base;
use Espo\Core\Exceptions\Error;

class Upload extends \Espo\Core\Upgrades\Actions\Base
{
    /**
     * Upload an upgrade/extension package
     *
     * @param  [type] $contents
     * @return string  ID of upgrade/extension process
     */
    public function run($data)
    {
        $processId = $this->createProcessId();

        $GLOBALS['log']->debug('Installation process ['.$processId.']: start upload the package.');

        $this->initialize();

        $this->beforeRunAction();

        $packagePath = $this->getPackagePath();
        $packageArchivePath = $this->getPackagePath(true);

        if (!empty($data)) {
            list($prefix, $contents) = explode(',', $data);
            $contents = base64_decode($contents);
        }

        $res = $this->getFileManager()->putContents($packageArchivePath, $contents);
        if ($res === false) {
            throw new Error('Could not upload the package.');
        }

        $this->unzipArchive();

        $this->isAcceptable();

        $this->afterRunAction();

        $this->finalize();

        $GLOBALS['log']->debug('Installation process ['.$processId.']: end upload the package.');

        return $processId;
    }
}
