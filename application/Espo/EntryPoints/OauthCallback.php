<?php


namespace Espo\EntryPoints;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class OauthCallback extends \Espo\Core\EntryPoints\Base
{
    public static $authRequired = false;

    public function run()
    {
        echo "SnappyCRM !!!";
    }
}

