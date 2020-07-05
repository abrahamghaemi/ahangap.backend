<?php


namespace Espo\ORM;

class SthCollection implements \IteratorAggregate
{
    private $entityManager = null;

    private $entityType;

    protected $selectParams = null;

    private $sth = null;

    private $sql = null;

    public function __construct(string $entityType, EntityManager $entityManager = null, array $selectParams = [])
    {
        $this->selectParams = $selectParams;
        $this->entityType = $entityType;
        $this->entityManager = $entityManager;
    }

    public function setSelectParams(array $selectParams)
    {
        $this->selectParams = $selectParams;
    }

    public function setQuery(?string $sql)
    {
        $this->sql = $sql;
    }

    public function executeQuery()
    {
        if ($this->sql) {
            $sql = $this->sql;
        } else {
            $sql = $this->entityManager->getQuery()->createSelectQuery($this->entityType, $this->selectParams);
        }
        $sth = $this->entityManager->getPdo()->prepare($sql);
        $sth->execute();

        $this->sth = $sth;
    }

    public function getIterator()
    {
        return (function () {
            while ($row = $this->fetchRow()) {
                $entity = $this->entityManager->getEntityFactory()->create($this->entityType);
                $entity->set($row);
                $entity->setAsFetched();
                $this->prepareEntity($entity);
                yield $entity;
            }
        })();
    }

    protected function fetchRow()
    {
        if (!$this->sth) {
            $this->executeQuery();
        }
        return $this->sth->fetch(\PDO::FETCH_ASSOC);
    }

    protected function prepareEntity(Entity $entity)
    {
    }
}
