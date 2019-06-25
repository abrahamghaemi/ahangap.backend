<?php

namespace Espo\Custom\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;

class OtpManager extends \Espo\Core\Templates\Controllers\Base
{
    public function actionSend($params, $data, $request)
    {
        if (!is_object($data)) throw new BadRequest();


        if (!$request->isPost()) {
            throw new BadRequest();
        }

        if (!$this->getAcl()->check($this->name, 'create')) {
            throw new Forbidden();
        }

        $service = $this->getRecordService();

        $data = $this->normlizationDate($data);
        $code = mt_rand(0000, 9999);
        $data->name = $code;
        $data->assignedUserId = "1";

        if ($entity = $service->create($data)) {
//            $this->sendOtp($data);
            return $entity->getValueMap();
        }

        throw new Error();
    }

    public function actionVerify($params, $data, $request)
    {
        $name = $params['message'];
        $entity = $this->getRecordService()->read($name);
        return $entity->getValueMap();


        if ($entity = $this->getRecordService()->update($id, $d)) {
            return $entity->getValueMap();
        }

        throw new Error();
    }

    public function sendOtp($data)
    {

        $sender = "1000596446";
        $message = "snapycloud verify code: " . $data->name;
        $api = new\Kavenegar\KavenegarApi("5435724C55454870633569354861705778766E303158543542583779356D7A6A");
        $api->Send($sender, $data->phone, $message);
        return true;
    }
}
