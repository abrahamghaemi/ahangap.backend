<?php


namespace Espo\SelectManagers;

class User extends \Espo\Core\SelectManagers\Base
{
    protected function access(&$result)
    {
        parent::access($result);

        if (!$this->getUser()->isAdmin()) {
            $result['whereClause'][] = [
                'isActive' => true,
                'type!=' => ['api']
            ];
        }
        if ($this->getAcl()->get('portalPermission') !== 'yes') {
            $result['whereClause'][] = [
                'OR' => [
                    ['type!=' => 'portal'],
                    ['id' => $this->getUser()->id]
                ]
            ];
        }

        if (!$this->getUser()->isSuperAdmin()) {
            $result['whereClause'][] = [
                'type!=' => 'super-admin'
            ];
        }

        $result['whereClause'][] = [
            'type!=' => 'system'
        ];
    }

    protected function filterActive(&$result)
    {
        $result['whereClause'][] = [
            'isActive' => true,
            'type' => ['regular', 'admin']
        ];
    }

    protected function filterActivePortal(&$result)
    {
        $result['whereClause'][] = [
            'isActive' => true,
            'type' => 'portal'
        ];
    }

    protected function filterPortal(&$result)
    {
        $result['whereClause'][] = [
            'type' => 'portal'
        ];
    }

    protected function filterApi(&$result)
    {
        $result['whereClause'][] = [
            'type' => 'api'
        ];
    }

    protected function filterActiveApi(&$result)
    {
        $result['whereClause'][] = [
            'isActive' => true,
            'type' => 'api'
        ];
    }

    protected function filterInternal(&$result)
    {
        $result['whereClause'][] = [
            'type!=' => ['portal', 'api', 'system']
        ];
    }

    protected function boolFilterOnlyMyTeam(&$result)
    {
        $this->addJoin('teams', $result);
        $result['whereClause'][] = [
        	'teamsMiddle.teamId' => $this->getUser()->getLinkMultipleIdList('teams')
        ];
        $this->setDistinct(true, $result);
    }

    protected function accessOnlyOwn(&$result)
    {
        $result['whereClause'][] = [
            'id' => $this->getUser()->id
        ];
    }

    protected function accessPortalOnlyOwn(&$result)
    {
        $result['whereClause'][] = [
            'id' => $this->getUser()->id
        ];
    }

    protected function accessOnlyTeam(&$result)
    {
        $this->setDistinct(true, $result);
        $this->addLeftJoin(['teams', 'teamsAccess'], $result);
        $result['whereClause'][] = [
            'OR' => [
                'teamsAccess.id' => $this->getUser()->getLinkMultipleIdList('teams'),
                'id' => $this->getUser()->id
            ]
        ];
    }
}
