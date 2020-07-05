<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class EmailAccount extends \Espo\Core\Controllers\Record
{
    public function postActionGetFolders($params, $data)
    {
        return $this->getRecordService()->getFolders([
            'host' => $data->host ?? null,
            'port' => $data->port ?? null,
            'ssl' =>  $data->ssl ?? false,
            'username' => $data->username ?? null,
            'password' => $data->password ?? null,
            'id' => $data->id ?? null,
            'emailAddress' => $data->emailAddress ?? null,
            'userId' => $data->userId ?? null,
        ]);
    }

    protected function checkControllerAccess()
    {
        if (!$this->getAcl()->check('EmailAccountScope')) {
            throw new Forbidden();
        }
    }

    public function postActionTestConnection($params, $data, $request)
    {
        if (is_null($data->password)) {
            $emailAccount = $this->getEntityManager()->getEntity('EmailAccount', $data->id);
            if (!$emailAccount || !$emailAccount->id) {
                throw new Error();
            }

            if ($emailAccount->get('assignedUserId') != $this->getUser()->id && !$this->getUser()->isAdmin()) {
                throw new Forbidden();
            }

            $data->password = $this->getContainer()->get('crypt')->decrypt($emailAccount->get('password'));
        }

        return $this->getRecordService()->testConnection(get_object_vars($data));
    }
}
