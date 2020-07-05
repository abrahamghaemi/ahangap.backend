<?php


namespace Espo\EntryPoints;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class ChangePassword extends \Espo\Core\EntryPoints\Base
{
    public static $authRequired = false;

    public function run()
    {
        $requestId = $_GET['id'];
        if (empty($requestId)) {
            throw new BadRequest();
        }

        $config = $this->getConfig();
        $themeManager = $this->getThemeManager();

        $p = $this->getEntityManager()->getRepository('PasswordChangeRequest')->where(array(
            'requestId' => $requestId
        ))->findOne();

        if (!$p) {
            throw new NotFound();
        }

        $runScript = "
            app.getController('PasswordChangeRequest', function (controller) {
                controller.doAction('passwordChange', '{$requestId}');
            });
        ";

        $this->getClientManager()->display($runScript);
    }

    protected function getThemeManager()
    {
        return $this->getContainer()->get('themeManager');
    }
}

