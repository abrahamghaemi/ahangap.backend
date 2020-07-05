<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Forbidden;

class EmailAddress extends \Espo\Core\Controllers\Record
{
    public function actionSearchInAddressBook($params, $data, $request)
    {
        if (!$this->getAcl()->checkScope('Email')) {
            throw new Forbidden();
        }
        if (!$this->getAcl()->checkScope('Email', 'create')) {
            throw new Forbidden();
        }
        $q = $request->get('q');
        $maxSize = intval($request->get('maxSize'));
        if (empty($maxSize) || $maxSize > 50) {
            $maxSize = $this->getConfig()->get('recordsPerPage', 20);
        }

        $onlyActual = $request->get('onlyActual') === 'true';

        return $this->getRecordService()->searchInAddressBook($q, $maxSize, $onlyActual);
    }
}
