<?php


namespace Espo\Entities;

class User extends \Espo\Core\Entities\Person
{
    public function isActive()
    {
        return $this->get('isActive');
    }

    public function isAdmin()
    {
        return $this->get('type') === 'admin' || $this->isSystem() || $this->isSuperAdmin();
    }

    public function isPortal()
    {
        return $this->get('type') === 'portal';
    }

    public function isPortalUser()
    {
        return $this->isPortal();
    }

    public function isRegular()
    {
        return $this->get('type') === 'regular' || ($this->has('type') && !$this->get('type'));
    }

    public function isApi()
    {
        return $this->get('type') === 'api';
    }

    public function isSystem()
    {
        return $this->get('type') === 'system';
    }

    public function isSuperAdmin()
    {
        return $this->get('type') === 'super-admin';
    }

    public function getTeamIdList()
    {
        if (!$this->has('teamsIds')) {
            $this->loadLinkMultipleField('teams');
        }
        return $this->get('teamsIds');
    }

    public function loadAccountField()
    {
        if ($this->get('contactId')) {
            $contact = $this->getEntityManager()->getEntity('Contact', $this->get('contactId'));
            if ($contact && $contact->get('accountId')) {
                $this->set('accountId', $contact->get('accountId'));
                $this->set('accountName', $contact->get('accountName'));
            }
        }
    }

    protected function _getName()
    {
        if (!array_key_exists('name', $this->valuesContainer) || !$this->valuesContainer['name']) {
            if ($this->get('userName')) {
                return $this->get('userName');
            }
        }
        return $this->valuesContainer['name'];
    }

    protected function _hasName()
    {
        if (array_key_exists('name', $this->valuesContainer)) {
            return true;
        }
        if ($this->has('userName')) {
            return true;
        }
        return false;
    }
}
