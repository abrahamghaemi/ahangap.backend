<?php


namespace Espo\Core;

use \Espo\Core\Exceptions\Error;

use \Espo\Core\Utils\Util;
use \Espo\Core\InjectableFactory;

class NotificatorFactory extends InjectableFactory
{
    public function create($entityType)
    {
        $normalizedName = Util::normilizeClassName($entityType);

        $className = '\\Espo\\Custom\\Notificators\\' . $normalizedName;
        if (!class_exists($className)) {
            $moduleName = $this->getMetadata()->getScopeModuleName($entityType);
            if ($moduleName) {
                $className = '\\Espo\\Modules\\' . $moduleName . '\\Notificators\\' . $normalizedName;
            } else {
                $className = '\\Espo\\Notificators\\' . $normalizedName;
            }
            if (!class_exists($className)) {
                $className = '\\Espo\\Core\\Notificators\\Base';
            }
        }

        return $this->createByClassName($className);
    }
}
