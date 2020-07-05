<?php


namespace Espo\Core\Console\Commands;

class AclCheck extends Base
{
    public function run($options)
    {
        $userId = $options['userId'] ?? null;
        $scope = $options['scope'] ?? null;
        $id = $options['id'] ?? null;
        $action = $options['action'] ?? null;

        if (empty($userId)) return;
        if (empty($scope)) return;
        if (empty($id)) return;

        $container = $this->getContainer();
        $entityManager = $container->get('entityManager');

        $user = $entityManager->getEntity('User', $userId);
        if (!$user) return;

        if ($user->isPortal()) {
            $portalIdList = $user->getLinkMultipleIdList('portals');
            foreach ($portalIdList as $portalId) {
                $application = new \Espo\Core\Portal\Application($portalId);
                $containerPortal = $application->getContainer();
                $entityManager = $containerPortal->get('entityManager');

                $user = $entityManager->getEntity('User', $userId);
                if (!$user) return;

                $result = $this->check($user, $scope, $id, $action, $containerPortal);
                if ($result) {
                    return 'true';
                }
            }
            return;
        }

        if ($this->check($user, $scope, $id, $action, $container)) {
            return 'true';
        }
    }

    protected function check($user, $scope, $id, $action, $container)
    {
        $entityManager = $container->get('entityManager');

        $entity = $entityManager->getEntity($scope, $id);
        if (!$entity) return;

        $aclManager = $container->get('aclManager');

        if ($aclManager->check($user, $entity, $action)) {
            return true;
        }
    }
}
