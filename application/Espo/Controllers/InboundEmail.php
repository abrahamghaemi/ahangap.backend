<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class InboundEmail extends \Espo\Core\Controllers\Record
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function postActionGetFolders($params, $data, $request)
    {
        return $this->getRecordService()->getFolders([
            'host' => $data->host ?? null,
            'port' => $data->port ?? null,
            'ssl' =>  $data->ssl ?? false,
            'username' => $data->username ?? null,
            'password' => $data->password ?? null,
            'id' => $data->id ?? null,
        ]);
    }

    public function postActionTestConnection($params, $data, $request)
    {
        if (is_null($data->password)) {
            $inboundEmail = $this->getEntityManager()->getEntity('InboundEmail', $data->id);
            if (!$inboundEmail || !$inboundEmail->id) {
                throw new Error();
            }
            $data->password = $this->getContainer()->get('crypt')->decrypt($inboundEmail->get('password'));
        }

        return $this->getRecordService()->testConnection(get_object_vars($data));
    }
}
