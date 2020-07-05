<?php


namespace Espo\Core\Console\Commands;

class Upgrade extends Base
{
    public function run()
    {
        $infoData = $this->getVersionInfo();
        if (!$infoData) return;

        $nextVersion = $infoData->nextVersion ?? null;
        $lastVersion = $infoData->lastVersion ?? null;

        $fromVersion = $this->getConfig()->get('version');

        fwrite(\STDOUT, "Current version is {$fromVersion}.\n");

        if (!$nextVersion) {
            echo "There are no available upgrades.\n";
            return;
        }

        fwrite(\STDOUT, "EspoCRM will be upgaded to version {$nextVersion} now. Type 'Y' to continue.\n");

        if (!$this->confirm()) {
            echo "Upgrade canceled.\n";
            return;
        }

        fwrite(\STDOUT, "Downloading...");

        $upgradePackageFilePath = $this->downloadFile($infoData->nextPackage);
        if (!$upgradePackageFilePath) return;

        fwrite(\STDOUT, "\n");

        fwrite(\STDOUT, "Upgrading... This may take a while...");

        $this->upgrade($upgradePackageFilePath);

        fwrite(\STDOUT, "\n");

        fwrite(\STDOUT, $resultText);

        $this->getFileManager()->unlink($upgradePackageFilePath);

        $app = new \Espo\Core\Application();
        $currentVerison = $app->getContainer()->get('config')->get('version');

        fwrite(\STDOUT, "Upgrade is complete. Current version is {$currentVerison}.\n");

        if ($lastVersion && $lastVersion !== $currentVerison && $fromVersion !== $currentVerison) {
            fwrite(\STDOUT, "Newer version is available. Run command again to upgrade.\n");
            return;
        }

        if ($lastVersion && $lastVersion === $currentVerison) {
            fwrite(\STDOUT, "You have the latest version.\n");
            return;
        }
    }

    protected function upgrade($filePath)
    {
        $app = new \Espo\Core\Application();
        $app->setupSystemUser();

        $upgradeManager = new \Espo\Core\UpgradeManager($app->getContainer());

        try {
            $fileData = file_get_contents($filePath);
            $fileData = 'data:application/zip;base64,' . base64_encode($fileData);

            $upgradeId = $upgradeManager->upload($fileData);
            $upgradeManager->install(['id' => $upgradeId]);
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage() . "\n");
        }
    }

    protected function confirm()
    {
        $fh = fopen('php://stdin', 'r');
        $inputLine = trim(fgets($fh));
        fclose($fh);
        if (strtolower($inputLine) !== 'y'){
            return false;
        }
        return true;
    }

    protected function getConfig()
    {
        return $this->getContainer()->get('config');
    }

    protected function getFileManager()
    {
        return $this->getContainer()->get('fileManager');
    }

    protected function getVersionInfo()
    {
        $url = 'https://s.espocrm.com/upgrade/next/';
        $url = $this->getConfig()->get('upgradeNextVersionUrl', $url);
        $url .= '?fromVersion=' . $this->getConfig()->get('version');

        $ch = curl_init();
        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, \CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        try {
            $data = json_decode($result);
        } catch (\Exception $e) {
            echo "Could not parse info about next version.\n";
            return;
        }

        if (!$data) {
            echo "Could not get info about next version.\n";
            return;
        }

        return $data;
    }

    protected function downloadFile($url)
    {
        $localFilePath = 'data/upload/upgrades/' . \Espo\Core\Utils\Util::generateId() . '.zip';
        $this->getFileManager()->putContents($localFilePath, '');

        if (is_file($url)) {
            copy($url, $localFilePath);
        } else {
            $options = [
                CURLOPT_FILE  => fopen($localFilePath, 'w'),
                CURLOPT_TIMEOUT => 3600,
                CURLOPT_URL => $url
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            curl_close($ch);
        }

        if (!$this->getFileManager()->isFile($localFilePath)) {
            echo "\nCould not download upgrade file.\n";
            $this->getFileManager()->unlink($localFilePath);
            return;
        }

        return realpath($localFilePath);
    }
}
