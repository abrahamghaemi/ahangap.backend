<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class Settings extends \Espo\Core\Controllers\Base
{

    protected function getConfigData()
    {
        $data = $this->getServiceFactory()->create('Settings')->getConfigData();

        $data->jsLibs = $this->getMetadata()->get('app.jsLibs');

        return $data;
    }

    public function actionRead($params, $data)
    {
        return $this->getConfigData();
    }

    public function actionUpdate($params, $data, $request)
    {
        return $this->actionPatch($params, $data, $request);
    }

    public function actionPatch($params, $data, $request)
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }

        if (!$request->isPut() && !$request->isPatch()) {
            throw new BadRequest();
        }

        $this->getServiceFactory()->create('Settings')->setConfigData($data);

        return $this->getConfigData();
    }

    public function postActionTestLdapConnection($params, $data)
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }

        if (!isset($data->password)) {
            $data->password = $this->getConfig()->get('ldapPassword');
        }

        $data = get_object_vars($data);

        $ldapUtils = new \Espo\Core\Utils\Authentication\LDAP\Utils();
        $options = $ldapUtils->normalizeOptions($data);

        $ldapClient = new \Espo\Core\Utils\Authentication\LDAP\Client($options);
        $ldapClient->bind(); //an exception if no connection

        return true;
    }
}
