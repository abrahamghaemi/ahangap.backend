<?php


namespace Espo\Core\Utils\Database\Schema;

use Doctrine\DBAL\Types\Type,
    Espo\Core\Utils\Util;

class Schema
{
    private $config;

    private $metadata;

    private $fileManager;

    private $entityManager;

    private $classParser;

    private $comparator;

    private $converter;

    private $databaseHelper;

    protected $fieldTypePaths = array(
        'application/Espo/Core/Utils/Database/DBAL/FieldTypes',
        'custom/Espo/Custom/Core/Utils/Database/DBAL/FieldTypes',
    );

    /**
     * Paths of rebuild action folders
     * @var array
     */
    protected $rebuildActionsPath = array(
        'corePath' => 'application/Espo/Core/Utils/Database/Schema/rebuildActions',
        'customPath' => 'custom/Espo/Custom/Core/Utils/Database/Schema/rebuildActions',
    );

    /**
     * Array of rebuildActions classes in format:
     *  array(
     *      'beforeRebuild' => array(...),
     *      'afterRebuild' => array(...),
     *  )
     * @var array
     */
    protected $rebuildActionClasses = null;

    public function __construct(\Espo\Core\Utils\Config $config, \Espo\Core\Utils\Metadata $metadata, \Espo\Core\Utils\File\Manager $fileManager, \Espo\Core\ORM\EntityManager $entityManager, \Espo\Core\Utils\File\ClassParser $classParser, \Espo\Core\Utils\Metadata\OrmMetadata $ormMetadata)
    {
        $this->config = $config;
        $this->metadata = $metadata;
        $this->fileManager = $fileManager;
        $this->entityManager = $entityManager;
        $this->classParser = $classParser;

        $this->databaseHelper = new \Espo\Core\Utils\Database\Helper($this->config);

        $this->comparator = new \Espo\Core\Utils\Database\DBAL\Schema\Comparator();
        $this->initFieldTypes();

        $this->converter = new \Espo\Core\Utils\Database\Converter($this->metadata, $this->fileManager, $this->config);
        $this->schemaConverter = new Converter($this->metadata, $this->fileManager, $this, $this->config);

        $this->ormMetadata = $ormMetadata;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    protected function getFileManager()
    {
        return $this->fileManager;
    }

    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    protected function getComparator()
    {
        return $this->comparator;
    }

    protected function getConverter()
    {
        return $this->converter;
    }

    protected function getClassParser()
    {
        return $this->classParser;
    }

    public function getPlatform()
    {
        return $this->getConnection()->getDatabasePlatform();
    }

    public function getDatabaseHelper()
    {
        return $this->databaseHelper;
    }

    public function getConnection()
    {
        return $this->getDatabaseHelper()->getDbalConnection();
    }

    protected function initFieldTypes()
    {
        foreach($this->fieldTypePaths as $path) {

            $typeList = $this->getFileManager()->getFileList($path, false, '\.php$');
            if ($typeList !== false) {
                foreach ($typeList as $name) {
                    $typeName = preg_replace('/Type\.php$/i', '', $name);
                    $dbalTypeName = strtolower($typeName);

                    $filePath = Util::concatPath($path, $typeName . 'Type');
                    $class = Util::getClassName($filePath);

                    if( ! Type::hasType($dbalTypeName) ) {
                        Type::addType($dbalTypeName, $class);
                    } else {
                        Type::overrideType($dbalTypeName, $class);
                    }

                    $dbTypeName = method_exists($class, 'getDbTypeName') ? $class::getDbTypeName() : $dbalTypeName;

                    $this->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping($dbTypeName, $dbalTypeName);
                }
            }
        }
    }

    /*
     * Rebuild database schema
     */
    public function rebuild($entityList = null)
    {
        if (!$this->getConverter()->process()) {
            return false;
        }

        $currentSchema = $this->getCurrentSchema();

        $metadataSchema = $this->schemaConverter->process($this->ormMetadata->getData(), $entityList);

        $this->initRebuildActions($currentSchema, $metadataSchema);
        $this->executeRebuildActions('beforeRebuild');

        $queries = $this->getDiffSql($currentSchema, $metadataSchema);

        $result = true;
        $connection = $this->getConnection();
        foreach ($queries as $sql) {
            $GLOBALS['log']->info('SCHEMA, Execute Query: '.$sql);
            try {
                $result &= (bool) $connection->executeQuery($sql);
            } catch (\Exception $e) {
                $GLOBALS['log']->alert('Rebuild database fault: '.$e);
                $result = false;
            }
        }

        $this->executeRebuildActions('afterRebuild');

        return (bool) $result;
    }

    /*
    * Get current database schema
    *
    * @return \Doctrine\DBAL\Schema\Schema
    */
    protected function getCurrentSchema()
    {
        return $this->getConnection()->getSchemaManager()->createSchema();
    }

    /*
    * Get SQL queries of database schema
    *
    * @params \Doctrine\DBAL\Schema\Schema $schema
    *
    * @return array - array of SQL queries
    */
    public function toSql(\Doctrine\DBAL\Schema\SchemaDiff $schema)   //Doctrine\DBAL\Schema\SchemaDiff | \Doctrine\DBAL\Schema\Schema
    {
        return $schema->toSaveSql($this->getPlatform());
        //return $schema->toSql($this->getPlatform()); //it can return with DROP TABLE
    }

    /*
    * Get SQL queries to get from one to another schema
    *
    * @return array - array of SQL queries
    */
    public function getDiffSql(\Doctrine\DBAL\Schema\Schema $fromSchema, \Doctrine\DBAL\Schema\Schema $toSchema)
    {
        $schemaDiff = $this->getComparator()->compare($fromSchema, $toSchema);

        return $this->toSql($schemaDiff); //$schemaDiff->toSql($this->getPlatform());
    }

    /**
     * Init Rebuild Actions, get all classes and create them
     * @return void
     */
    protected function initRebuildActions($currentSchema = null, $metadataSchema = null)
    {
        $methods = array('beforeRebuild', 'afterRebuild');

        $this->getClassParser()->setAllowedMethods($methods);
        $rebuildActions = $this->getClassParser()->getData($this->rebuildActionsPath);

        $classes = array();
        foreach ($rebuildActions as $actionName => $actionClass) {
            $rebuildActionClass = new $actionClass($this->metadata, $this->config, $this->entityManager);
            if (isset($currentSchema)) {
                $rebuildActionClass->setCurrentSchema($currentSchema);
            }
            if (isset($metadataSchema)) {
                $rebuildActionClass->setMetadataSchema($metadataSchema);
            }

            foreach ($methods as $methodName) {
                if (method_exists($rebuildActionClass, $methodName)) {
                    $classes[$methodName][] = $rebuildActionClass;
                }
            }
        }

        $this->rebuildActionClasses = $classes;
    }

    /**
     * Execute actions for RebuildAction classes
     * @param  string $action action name, possible values 'beforeRebuild' | 'afterRebuild'
     * @return void
     */
    protected function executeRebuildActions($action = 'beforeRebuild')
    {
        if (!isset($this->rebuildActionClasses)) {
            $this->initRebuildActions();
        }

        if (isset($this->rebuildActionClasses[$action])) {
            foreach ($this->rebuildActionClasses[$action] as $rebuildActionClass) {
                $rebuildActionClass->$action();
            }
        }
    }
}
