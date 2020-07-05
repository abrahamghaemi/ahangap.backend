<?php


namespace Espo\Controllers;

use Espo\EntryPoints\Avatar;
use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class User extends \Espo\Core\Controllers\Record
{
    public function actionAcl($params, $data, $request)
    {
        $userId = $request->get('id');
        if (empty($userId)) {
            throw new Error();
        }

        if (!$this->getUser()->isAdmin() && $this->getUser()->id != $userId) {
            throw new Forbidden();
        }

        $user = $this->getEntityManager()->getEntity('User', $userId);
        if (empty($user)) {
            throw new NotFound();
        }

        return $this->getAclManager()->getMap($user);
    }

    public function actionSubCheck($params, $data, $request)
    {
     $id = $_SERVER['HTTP_CLIENTID'];
    $entity = $this->getRecordService()->read($id);



        $e = $entity->getValueMap();
        return ['status' => $e->subscribed];
    }

    public function postActionChangeOwnPassword($params, $data, $request)
    {
        if (!property_exists($data, 'password') || !property_exists($data, 'currentPassword')) {
            throw new BadRequest();
        }
        return $this->getService('User')->changePassword($this->getUser()->id, $data->password, true, $data->currentPassword);
    }

    public function postActionChangePasswordByRequest($params, $data, $request)
    {
        if (empty($data->requestId) || empty($data->password)) {
            throw new BadRequest();
        }

        $p = $this->getEntityManager()->getRepository('PasswordChangeRequest')->where(array(
            'requestId' => $data->requestId
        ))->findOne();

        if (!$p) {
            throw new Forbidden();
        }
        $userId = $p->get('userId');
        if (!$userId) {
            throw new Error();
        }

        $this->getEntityManager()->removeEntity($p);

        if ($this->getService('User')->changePassword($userId, $data->password)) {
            return array(
                'url' => $p->get('url')
            );
        }
    }

    public function postActionPasswordChangeRequest($params, $data, $request)
    {
        if (empty($data->userName) || empty($data->emailAddress)) {
            throw new BadRequest();
        }

        $userName = $data->userName;
        $emailAddress = $data->emailAddress;
        $url = null;
        if (!empty($data->url)) {
            $url = $data->url;
        }

        return $this->getService('User')->passwordChangeRequest($userName, $emailAddress, $url);
    }

    public function postActionGenerateNewApiKey($params, $data, $request)
    {
        if (empty($data->id)) throw new BadRequest();
        if (!$this->getUser()->isAdmin()) throw new Forbidden();
        return $this->getRecordService()->generateNewApiKeyForEntity($data->id)->getValueMap();
    }

    public function actionCreateLink($params, $data, $request)
    {
        if (!$this->getUser()->isAdmin()) throw new Forbidden();

        return parent::actionCreateLink($params, $data, $request);
    }

    public function actionRemoveLink($params, $data, $request)
    {
        if (!$this->getUser()->isAdmin()) throw new Forbidden();

        return parent::actionRemoveLink($params, $data, $request);
    }

    public function actionCreateUser($params, $data, $request)
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

        if ($entity = $service->create($data)) {
            return $entity->getValueMap();
        }

        throw new Error();
    }

    public function actionAvatar()
    {
        $app = new \Espo\Core\Application();
        $app->runEntryPoint('avatar');
        exit;
        $avatar = new Avatar();
        $avatar->run();
        exit;
    }
}
