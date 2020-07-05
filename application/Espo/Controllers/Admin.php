<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class Admin extends \Espo\Core\Controllers\Base
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function postActionRebuild($params, $data, $request)
    {
        if (!$request->isPost()) {
            throw new BadRequest();
        }
        $result = $this->getContainer()->get('dataManager')->rebuild();

        return $result;
    }

    public function postActionClearCache($params)
    {
        $result = $this->getContainer()->get('dataManager')->clearCache();
        return $result;
    }

    public function actionJobs()
    {
        $scheduledJob = $this->getContainer()->get('scheduledJob');

        return $scheduledJob->getAvailableList();
    }

    public function postActionUploadUpgradePackage($params, $data)
    {
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }
        $upgradeManager = new \Espo\Core\UpgradeManager($this->getContainer());

        $upgradeId = $upgradeManager->upload($data);
        $manifest = $upgradeManager->getManifest();

        return array(
            'id' => $upgradeId,
            'version' => $manifest['version'],
        );
    }

    public function postActionRunUpgrade($params, $data)
    {
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }

        $upgradeManager = new \Espo\Core\UpgradeManager($this->getContainer());
        $upgradeManager->install(get_object_vars($data));

        return true;
    }

    public function actionCronMessage($params)
    {
        return $this->getContainer()->get('scheduledJob')->getSetupMessage();
    }

    public function actionAdminNotificationList($params)
    {
        $adminNotificationManager = new \Espo\Core\Utils\AdminNotificationManager($this->getContainer());
        return $adminNotificationManager->getNotificationList();
    }

    public function actionSystemRequirementList($params)
    {
        $systemRequirementManager = new \Espo\Core\Utils\SystemRequirements($this->getContainer());
        return $systemRequirementManager->getAllRequiredList();
    }
}
