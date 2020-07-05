<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\Forbidden;

class Extension extends \Espo\Core\Controllers\Record
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function actionUpload($params, $data, $request)
    {
        if (!$request->isPost()) {
            throw new Forbidden();
        }

        $manager = new \Espo\Core\ExtensionManager($this->getContainer());

        $id = $manager->upload($data);
        $manifest = $manager->getManifest();

        return array(
            'id' => $id,
            'version' => $manifest['version'],
            'name' => $manifest['name'],
            'description' => $manifest['description'],
        );
    }

    public function actionInstall($params, $data, $request)
    {
        if (!$request->isPost()) {
            throw new Forbidden();
        }
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }

        $manager = new \Espo\Core\ExtensionManager($this->getContainer());

        $manager->install(get_object_vars($data));

        return true;
    }

    public function actionUninstall($params, $data, $request)
    {
        if (!$request->isPost()) {
            throw new Forbidden();
        }
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }

        $manager = new \Espo\Core\ExtensionManager($this->getContainer());
        $manager->uninstall(get_object_vars($data));
        return true;
    }

    public function actionCreate($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionUpdate($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionPatch($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionListLinked($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionDelete($params, $data, $request)
    {
        if (!$request->isDelete()) {
            throw BadRequest();
        }
        if ($this->getConfig()->get('restrictedMode')) {
            if (!$this->getUser()->isSuperAdmin()) {
                throw new Forbidden();
            }
        }
        $manager = new \Espo\Core\ExtensionManager($this->getContainer());
        $manager->delete($params);
        return true;
    }

    public function actionMassUpdate($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionMassDelete($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionCreateLink($params, $data, $request)
    {
        throw new Forbidden();
    }

    public function actionRemoveLink($params, $data, $request)
    {
        throw new Forbidden();
    }
}
