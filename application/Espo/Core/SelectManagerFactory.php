<?php


namespace Espo\Core;

use \Espo\Core\Exceptions\Error;
use \Espo\Core\Utils\Util;

use \Espo\Core\InjectableFactory;

class SelectManagerFactory
{
    private $entityManager;

    private $user;

    private $acl;

    private $metadata;

    private $injectableFactory;

    private $FieldManagerUtil;

    public function __construct(
        $entityManager,
        \Espo\Entities\User $user,
        Acl $acl,
        AclManager $aclManager,
        Utils\Metadata $metadata,
        Utils\Config $config,
        Utils\FieldManagerUtil $fieldManagerUtil,
        InjectableFactory $injectableFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->acl = $acl;
        $this->aclManager = $aclManager;
        $this->metadata = $metadata;
        $this->config = $config;
        $this->fieldManagerUtil = $fieldManagerUtil;
        $this->injectableFactory = $injectableFactory;
    }

    public function create(string $entityType, ?\Espo\Entities\User $user = null) : \Espo\Core\SelectManagers\Base
    {
        $normalizedName = Util::normilizeClassName($entityType);

        $className = '\\Espo\\Custom\\SelectManagers\\' . $normalizedName;
        if (!class_exists($className)) {
            $moduleName = $this->metadata->getScopeModuleName($entityType);
            if ($moduleName) {
                $className = '\\Espo\\Modules\\' . $moduleName . '\\SelectManagers\\' . $normalizedName;
            } else {
                $className = '\\Espo\\SelectManagers\\' . $normalizedName;
            }
            if (!class_exists($className)) {
                $className = '\\Espo\\Core\\SelectManagers\\Base';
            }
        }

        if ($user) {
            $acl = $this->aclManager->createUserAcl($user);
        } else {
            $acl = $this->acl;
            $user = $this->user;
        }

        $selectManager = new $className(
            $this->entityManager,
            $user,
            $acl,
            $this->aclManager,
            $this->metadata,
            $this->config,
            $this->fieldManagerUtil,
            $this->injectableFactory
        );
        $selectManager->setEntityType($entityType);

        return $selectManager;
    }
}
