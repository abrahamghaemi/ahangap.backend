<?php


namespace Espo\EntryPoints;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;
use \Espo\Core\Exceptions\Error;

class LogoImage extends Image
{
    public static $authRequired = false;

    protected $allowedRelatedTypeList = ['Settings', 'Portal'];

    protected $allowedFieldList = ['companyLogo'];

    public function run()
    {
        $this->imageSizes['small-logo'] = array(181, 44);

        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $id = $this->getConfig()->get('companyLogoId');
        }

        if (empty($id)) {
            throw new NotFound();
        }

        $size = null;
        if (!empty($_GET['size'])) {
            $size = $_GET['size'];
        }

        $this->show($id, $size);
    }
}

