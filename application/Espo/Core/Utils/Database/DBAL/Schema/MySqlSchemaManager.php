<?php


namespace Espo\Core\Utils\Database\DBAL\Schema;

use Doctrine\DBAL\Events;
use Doctrine\DBAL\Event\SchemaIndexDefinitionEventArgs;

class MySqlSchemaManager extends \Doctrine\DBAL\Schema\MySqlSchemaManager
{
    /* Espo: default value for MariaDB 10.2.7+ */
    protected $mariaDb1027;
    /* Espo: end */

    /* Espo */
    const LENGTH_LIMIT_LONGTEXT = 4294967295;
    const LENGTH_LIMIT_TINYTEXT   = 255;
    const LENGTH_LIMIT_TEXT       = 65535;
    const LENGTH_LIMIT_MEDIUMTEXT = 16777215;
    const LENGTH_LIMIT_TINYBLOB   = 255;
    const LENGTH_LIMIT_BLOB       = 65535;
    const LENGTH_LIMIT_MEDIUMBLOB = 16777215;
    /* Espo: end */

    public function createSchema()
    {
        $sequences = array();
        if ($this->_platform->supportsSequences()) {
            $sequences = $this->listSequences();
        }
        $tables = $this->listTables();

        return new Schema($tables, $sequences, $this->createSchemaConfig());
    }

    public function listTables()
    {
        $tableNames = $this->listTableNames();

        $tables = array();
        foreach ($tableNames as $tableName) {
            $tables[] = $this->listTableDetails($tableName);
        }

        return $tables;
    }

    public function listTableDetails($tableName)
    {
        $columns = $this->listTableColumns($tableName);
        $foreignKeys = array();
        if ($this->_platform->supportsForeignKeyConstraints()) {
            $foreignKeys = $this->listTableForeignKeys($tableName);
        }
        $indexes = $this->listTableIndexes($tableName);

        return new Table($tableName, $columns, $indexes, $foreignKeys, false, array());
    }

    public function listTableIndexes($table)
    {
        $sql = $this->_platform->getListTableIndexesSQL($table, $this->_conn->getDatabase());

        $tableIndexes = $this->_conn->fetchAll($sql);

        return $this->_getPortableTableIndexesList($tableIndexes, $table);
    }

    protected function _getPortableTableIndexesList($tableIndexes, $tableName=null)
    {
        foreach($tableIndexes as $k => $v) {
            $v = array_change_key_case($v, CASE_LOWER);
            if($v['key_name'] == 'PRIMARY') {
                $v['primary'] = true;
            } else {
                $v['primary'] = false;
            }
            if (strpos($v['index_type'], 'FULLTEXT') !== false) {
                $v['flags'] = array('FULLTEXT');
            }
            $tableIndexes[$k] = $v;
        }

        $result = array();
        foreach($tableIndexes as $tableIndex) {

            $indexName = $keyName = $tableIndex['key_name'];
            if ($tableIndex['primary']) {
                $keyName = 'primary';
            }
            $keyName = strtolower($keyName);

            if (!isset($result[$keyName])) {
                $result[$keyName] = array(
                    'name' => $indexName,
                    'columns' => array($tableIndex['column_name']),
                    'unique' => $tableIndex['non_unique'] ? false : true,
                    'primary' => $tableIndex['primary'],
                    'flags' => isset($tableIndex['flags']) ? $tableIndex['flags'] : array(),
                );
            } else {
                $result[$keyName]['columns'][] = $tableIndex['column_name'];
            }
        }

        $eventManager = $this->_platform->getEventManager();

        $indexes = array();
        foreach($result as $indexKey => $data) {
            $index = null;
            $defaultPrevented = false;

            if (null !== $eventManager && $eventManager->hasListeners(Events::onSchemaIndexDefinition)) {
                $eventArgs = new SchemaIndexDefinitionEventArgs($data, $tableName, $this->_conn);
                $eventManager->dispatchEvent(Events::onSchemaIndexDefinition, $eventArgs);

                $defaultPrevented = $eventArgs->isDefaultPrevented();
                $index = $eventArgs->getIndex();
            }

            if ( ! $defaultPrevented) {
                $index = new Index($data['name'], $data['columns'], $data['unique'], $data['primary'], $data['flags']);
            }

            if ($index) {
                $indexes[$indexKey] = $index;
            }
        }

        return $indexes;
    }

    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        $dbType = strtolower($tableColumn['type']);
        $dbType = strtok($dbType, '(), ');
        if (isset($tableColumn['length'])) {
            $length = $tableColumn['length'];
        } else {
            $length = strtok('(), ');
        }

        $fixed = null;

        if ( ! isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }

        $scale = null;
        $precision = null;

        $type = $this->_platform->getDoctrineTypeMapping($dbType);

        // In cases where not connected to a database DESCRIBE $table does not return 'Comment'
        if (isset($tableColumn['comment'])) {
            $type = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
            $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);
        }

        switch ($dbType) {
            case 'char':
                $fixed = true;
                break;
            case 'float':
            case 'double':
            case 'real':
            case 'numeric':
            case 'decimal':
                if(preg_match('([A-Za-z]+\(([0-9]+)\,([0-9]+)\))', $tableColumn['type'], $match)) {
                    $precision = $match[1];
                    $scale = $match[2];
                    $length = null;
                }
                break;
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'integer':
            case 'bigint':
            case 'tinyblob':
            case 'mediumblob':
            case 'longblob':
            case 'blob':
            case 'year':
                $length = null;
                break;

            /* Espo: fix a problem of changing text field type */
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                // snappbit: fix a problem of musql80platrom  "call undefinded method"
                $length = $this->getClobTypeLength($dbType);
                break;
            /* Espo: end */
        }

        $length = ((int) $length == 0) ? null : (int) $length;

        /* Espo: default value for MariaDB 10.2.7+ */
        $columnDefault = isset($tableColumn['default']) ? $tableColumn['default'] : null;
        if ($this->isMariaDb1027()) {
            $columnDefault = $this->getMariaDb1027ColumnDefault($this->_platform, $columnDefault);
        }
        /* Espo: end */

        $options = array(
            'length'        => $length,
            'unsigned'      => (bool) (strpos($tableColumn['type'], 'unsigned') !== false),
            'fixed'         => (bool) $fixed,
            'default'       => /* Espo: default value for MariaDB 10.2.7+ */ $columnDefault /* Espo: end */,
            'notnull'       => (bool) ($tableColumn['null'] != 'YES'),
            'scale'         => null,
            'precision'     => null,
            'autoincrement' => (bool) (strpos($tableColumn['extra'], 'auto_increment') !== false),
            'comment'       => (isset($tableColumn['comment'])) ? $tableColumn['comment'] : null
        );

        if ($scale !== null && $precision !== null) {
            $options['scale'] = $scale;
            $options['precision'] = $precision;
        }

        return new Column($tableColumn['field'], \Doctrine\DBAL\Types\Type::getType($type), $options);
    }

    /* Espo: default value for MariaDB 10.2.7+ */
    protected function isMariaDb1027()
    {
        if (!isset($this->mariaDb1027)) {
            $version = $this->_conn->fetchColumn("select version()");

            $this->mariaDb1027 = false;
            if (preg_match('/mariadb/i', $version) && version_compare($version, '10.2.7') >= 0) {
                $this->mariaDb1027 = true;
            }
        }

        return $this->mariaDb1027;
    }

    private function getMariaDb1027ColumnDefault($platform, $columnDefault)
    {
        if ($columnDefault === 'NULL' || $columnDefault === null) {
            return null;
        }
        if ($columnDefault[0] === "'") {
            return stripslashes(
                str_replace("''", "'",
                    preg_replace('/^\'(.*)\'$/', '$1', $columnDefault)
                )
            );
        }
        switch ($columnDefault) {
            case 'current_timestamp()':
                return $platform->getCurrentTimestampSQL();
            case 'curdate()':
                return $platform->getCurrentDateSQL();
            case 'curtime()':
                return $platform->getCurrentTimeSQL();
        }
        return $columnDefault;
    }
    /* Espo: end */

    /* Espo: fix a problem of changing text field type */
    public function getClobTypeLength($type)
    {
        switch ($type) {
            case 'tinytext':
                return static::LENGTH_LIMIT_TINYTEXT;
                break;

            case 'text':
                return static::LENGTH_LIMIT_TEXT;
                break;

            case 'mediumtext':
                return static::LENGTH_LIMIT_MEDIUMTEXT;
                break;

            case 'longtext':
                return static::LENGTH_LIMIT_LONGTEXT;
                break;
        }
    }
    /* Espo: end */
}
