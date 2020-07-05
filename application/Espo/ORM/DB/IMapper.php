<?php


namespace Espo\ORM\DB;
use Espo\ORM\IEntity;
use Espo\ORM\Classes\EntityFactory;

interface IMapper
{
    /**
     * Selects entity by id.
     */
    function selectById(IEntity $entity, $id, ?array $params = null) : ?IEntity;

    /**
     * Selects list of entitys according to given parameters.
     *
     * @return array Array of entities or collection.
     */
    function select(IEntity $entity, ?array $params = null);

    /**
     * Invokes aggregate function and returns a value.
     *
     * @return mixed Result of the aggregation
     */
    function aggregate(IEntity $entity, ?array $params, string $aggregation, string $aggregationBy);

    /**
     * Returns count of records according to given parameters.
     *
     * @return int Count of record
     */
    function count(IEntity $entity, ?array $params = null);

    /**
     * Returns max value of the attribute in the select according to given parameters.
     *
     * @param IEntity $entity
     * @param array $params Parameters
     * @param string $attribute Needed attribute.
     * @return mixed Max value
     */
    function max(IEntity $entity, ?array $params, string $attribute);

    /**
     * Returns min value of the attribute in the select according to given parameters.
     *
     * @return mixed Min value
     */
    function min(IEntity $entity, ?array $params, string $attribute);

    /**
     * Returns sum value of the attribute in the select according to given parameters.
     *
     * @return mixed Sum value
     */
    function sum(IEntity $entity, ?array $params, string $attribute);

    /**
     * Selects related entity or list of entitys.
     *
     * @return array List of entitys or total count if $totalCount was passed as true
     */
    function selectRelated(IEntity $entity, $relName, $params, $totalCount);

    /**
     * Returns count of related records according to given parameters.
     *
     * @return int Count of records
     */
    function countRelated(IEntity $entity, $relName, $params);

    /**
     * Links entity with another one.
     *
     * @return bool True if success
     */
    function addRelation(IEntity $entity, string $relationName, $id = null, $relEntity = null, $data = null);

    /**
     * Removes relation of entity with certain record.
     *
     * @return bool True if success
     */
    function removeRelation(IEntity $entity, string $relationName, $id = null, $all = false, IEntity $relEntity = null);

    /**
     * Removes all relations of entity of specified relation name.
     *
     * @return bool True if success
     */
    function removeAllRelations(IEntity $entity, string $relationName);

    /**
     * Insert entity into db.
     *
     * @return bool True if success
     */
    function insert(IEntity $entity);

    /**
     * Updates entity in db.
     *
     * @return bool True if success
     */
    function update(IEntity $entity);


    /**
     * Deletes entity.
     * (Marks as deleted)
     *
     * @return bool True if success
     */
    function delete(IEntity $entity);

    /**
     * Sets class name of a model collection that will be returned by operations such as select.
     *
     */
    function setCollectionClass(string $collectionClass);

    function deleteFromDb(string $entityType, $id, $onlyDeleted = false);
}
