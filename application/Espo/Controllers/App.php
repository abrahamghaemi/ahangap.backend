<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\BadRequest;

class App extends \Espo\Core\Controllers\Base
{
    public function actionUser()
    {
        if($_SERVER['HTTP_CLIENTID']) {
             $data = $this->getServiceFactory()->create('App')->getUserData();

             return [
                 'token' => $data['token'],
                 'help' => 'in header send App-Authorization: base64encode(username:token)'

             ];
        }
        return $this->getServiceFactory()->create('App')->getUserData();
    }

    public function postActionDestroyAuthToken($params, $data)
    {
        if (empty($data->token)) {
            throw new BadRequest();
        }

        $auth = new \Espo\Core\Utils\Auth($this->getContainer());
        return $auth->destroyAuthToken($data->token);
    }

    public function actionAsset($params, $data, $requst)
    {
        $app = new \Espo\Core\Application();
        $app->runEntryPoint('image');
        exit;
    }

    public function actionSubCheck($params, $data)
    {
        $userData = $this->getServiceFactory()->create('App')->getUserData();
        return $userData['user']->subscribed;
    }

}
