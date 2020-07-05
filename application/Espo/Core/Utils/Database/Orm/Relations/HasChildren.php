<?php


namespace Espo\Core\Utils\Database\Orm\Relations;

class HasChildren extends Base
{
    protected function load($linkName, $entityName)
    {
        $foreignLinkName = $this->getForeignLinkName();
        $foreignEntityName = $this->getForeignEntityName();

        $isStub = !$this->getMetadata()->get(['entityDefs', $entityName, 'fields', $linkName]);

        return [
            $entityName => [
                'fields' => [
                       $linkName.'Ids' => [
                        'type' => 'jsonArray',
                        'notStorable' => true,
                        'isLinkStub' => $isStub,
                    ],
                    $linkName.'Names' => [
                        'type' => 'jsonObject',
                        'notStorable' => true,
                        'isLinkStub' => $isStub,
                    ],
                ],
                'relations' => [
                    $linkName => [
                        'type' => 'hasChildren',
                        'entity' => $foreignEntityName,
                        'foreignKey' => $foreignLinkName.'Id',
                        'foreignType' => $foreignLinkName.'Type',
                        'foreign' => $foreignLinkName
                    ],
                ],
            ],
        ];
    }
}
