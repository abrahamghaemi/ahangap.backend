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
            $this->sendOtp($data);
            return true;
        }

        throw new Error();
    }

    public function actionVerify($params, $data, $request)
    {
        $name = $data->message;
		$phone = $data->phone;

		$entityManager = $this->getEntityManager();
        $entity = $entityManager->getRepository('OtpManager')->where([
            'phone' => $phone,
			'isClose' => false
        ])->order('createdAt')->findOne();


		if(!is_object($entity) || $entity->get('name') != $name) {
			throw new Error();
		}


        if ( $entity->has('id') && $entity = $this->getRecordService()->update($entity->get('id'),['status' => true, 'isClose' => true])) {

            $entity = $entityManager->getRepository('User')->where([
                'phoneNumber' => $phone
            ])->findOne();

            if($entity) {
                $response = $entity->getValueMap();
                unset($response->password);
                return $response;
            }

            return true;
        }

        throw new Error();
    }

    public function sendOtp($data)
    {

        $sender = "2000004346";
        $message = "snapycloud verify code: " . $data->name;
        $api = new\Kavenegar\KavenegarApi("5435724C55454870633569354861705778766E303158543542583779356D7A6A");
        $api->Send($sender, $data->phone, $message);
        return true;
    }
}
