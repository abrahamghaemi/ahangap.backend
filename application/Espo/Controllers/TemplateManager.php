<?php


namespace Espo\Controllers;

use Espo\Core\Utils as Utils;
use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class TemplateManager extends \Espo\Core\Controllers\Base
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function getActionGetTemplate($params, $data, $request)
    {
        $name = $request->get('name');
        if (empty($name)) throw new BadRequest();
        $scope = $request->get('scope');
        $module = null;
        $module = $this->getMetadata()->get(['app', 'templates', $name, 'module']);
        $hasSubject = !$this->getMetadata()->get(['app', 'templates', $name, 'noSubject']);

        $templateFileManager = $this->getContainer()->get('templateFileManager');

        $returnData = (object) [];
        $returnData->body = $templateFileManager->getTemplate($name, 'body', $scope, $module);

        if ($hasSubject) {
            $returnData->subject = $templateFileManager->getTemplate($name, 'subject', $scope, $module);
        }

        return $returnData;
    }

    public function postActionSaveTemplate($params, $data)
    {
        $scope = null;
        if (empty($data->name)) {
            throw new BadRequest();
        }
        if (!empty($data->scope)) {
            $scope = $data->scope;
        }

        $templateFileManager = $this->getContainer()->get('templateFileManager');

        if (isset($data->subject)) {
            $templateFileManager->saveTemplate($data->name, 'subject', $data->subject, $scope);
        }

        if (isset($data->body)) {
            $templateFileManager->saveTemplate($data->name, 'body', $data->body, $scope);
        }

        return true;
    }

    public function postActionResetTemplate($params, $data)
    {
        $scope = null;
        if (empty($data->name)) {
            throw new BadRequest();
        }
        if (!empty($data->scope)) {
            $scope = $data->scope;
        }

        $module = null;
        $module = $this->getMetadata()->get(['app', 'templates', $data->name, 'module']);
        $hasSubject = !$this->getMetadata()->get(['app', 'templates', $data->name, 'noSubject']);

        $templateFileManager = $this->getContainer()->get('templateFileManager');

        if ($hasSubject) {
            $templateFileManager->resetTemplate($data->name, 'subject', $scope);
        }

        $templateFileManager->resetTemplate($data->name, 'body', $scope);

        $returnData = (object) [];
        $returnData->body = $templateFileManager->getTemplate($data->name, 'body', $scope, $module);

        if ($hasSubject) {
            $returnData->subject = $templateFileManager->getTemplate($data->name, 'subject', $scope, $module);
        }

        return $returnData;
    }
}
