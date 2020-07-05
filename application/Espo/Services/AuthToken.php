<?php


namespace Espo\Services;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\Error;
use \Espo\Core\Exceptions\NotFound;

class AuthToken extends Record
{
    protected $actionHistoryDisabled = true;
}
