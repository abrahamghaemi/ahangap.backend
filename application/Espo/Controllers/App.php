<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2019 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

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

    public function actionVersion()
    {
        return [
            'package' => "package.name",
            'version' => '1.3.1',
            'releaseAt' => date("Y-m-d"),
            'cdn' => 'https://ahangap.com/ahangap_v1.3.apk'
        ];
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
