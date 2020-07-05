<?php


namespace Espo\Services;

use \Espo\ORM\Entity;

class Team extends Record
{
    protected function init()
    {
        $this->addDependency('fileManager');
    }

    protected $linkSelectParams = array(
        'users' => array(
            'additionalColumns' => array(
                'role' => 'teamRole'
            )
        )
    );

    protected function getFileManager()
    {
        return $this->getInjection('fileManager');
    }

    public function afterUpdateEntity(Entity $entity, $data)
    {
        parent::afterUpdateEntity($entity, $data);
        if (property_exists($data, 'rolesIds')) {
            $this->clearRolesCache();
        }
    }

    protected function clearRolesCache()
    {
        $this->getFileManager()->removeInDir('data/cache/application/acl');
    }

    public function link($id, $link, $foreignId)
    {
        $result = parent::link($id, $link, $foreignId);

        if ($link === 'users') {
            $this->getFileManager()->removeFile('data/cache/application/acl/' . $foreignId . '.php');
        }

        return $result;
    }

    public function unlink($id, $link, $foreignId)
    {
        $result = parent::unlink($id, $link, $foreignId);

        if ($link === 'users') {
            $this->getFileManager()->removeFile('data/cache/application/acl/' . $foreignId . '.php');
        }

        return $result;
    }

    public function massLink($id, $link, $where, $selectData = null)
    {
        $result = parent::massLink($id, $link, $where, $selectData);

        if ($link === 'users') {
            $this->getFileManager()->removeInDir('data/cache/application/acl');
        }

        return $result;
    }
}
