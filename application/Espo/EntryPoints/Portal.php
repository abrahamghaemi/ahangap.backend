<?php


namespace Espo\EntryPoints;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class Portal extends \Espo\Core\EntryPoints\Base
{
    public static $authRequired = false;

    public function run($data = array())
    {
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
        } else if (!empty($data['id'])) {
            $id = $data['id'];
        } else {
            $url = $_SERVER['REQUEST_URI'];
            $id = explode('/', $url)[count(explode('/', $_SERVER['SCRIPT_NAME'])) - 1];

            if (!isset($id)) {
                $url = $_SERVER['REDIRECT_URL'];
                $id = explode('/', $url)[count(explode('/', $_SERVER['SCRIPT_NAME'])) - 1];
            }

            if (!$id) {
                $id = $this->getConfig()->get('defaultPortalId');
            }
            if (!$id) {
                throw new NotFound();
            }
        }

        $application = new \Espo\Core\Portal\Application($id);
        $application->setBasePath($this->getContainer()->get('clientManager')->getBasePath());
        $application->runClient();
    }
}
