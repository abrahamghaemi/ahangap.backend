<?php


namespace Espo\Core\Utils;

use Espo\Core\Exceptions\Error;

class SystemRequirements
{
    private $container;

    private $systemHelper;

    private $databaseHelper;

    public function __construct(\Espo\Core\Container $container)
    {
        $this->container = $container;
        $this->systemHelper = new \Espo\Core\Utils\System();
        $this->databaseHelper = new \Espo\Core\Utils\Database\Helper($this->getConfig());
    }

    protected function getContainer()
    {
        return $this->container;
    }

    protected function getConfig()
    {
        return $this->getContainer()->get('config');
    }

    protected function getFileManager()
    {
        return $this->getContainer()->get('fileManager');
    }

    protected function getSystemHelper()
    {
        return $this->systemHelper;
    }

    protected function getDatabaseHelper()
    {
        return $this->databaseHelper;
    }

    public function getAllRequiredList($requiredOnly = false)
    {
        return [
            'php' => $this->getPhpRequiredList($requiredOnly),
            'database' => $this->getDatabaseRequiredList($requiredOnly),
            'permission' => $this->getRequiredPermissionList($requiredOnly),
        ];
    }

    public function getRequiredListByType($type, $requiredOnly = false, array $additionalData = null)
    {
        switch ($type) {
            case 'php':
                return $this->getPhpRequiredList($requiredOnly, $additionalData);
                break;

            case 'database':
                return $this->getDatabaseRequiredList($requiredOnly, $additionalData);
                break;

            case 'permission':
                return $this->getRequiredPermissionList($requiredOnly, $additionalData);
                break;
        }

        return [];
    }

    /**
     * Get required php params
     * @return array
     */
    protected function getPhpRequiredList($requiredOnly, array $additionalData = null)
    {
        $requiredList = [
            'requiredPhpVersion',
            'requiredPhpLibs',
        ];

        if (!$requiredOnly) {
            $requiredList = array_merge($requiredList, [
                'recommendedPhpLibs',
                'recommendedPhpParams',
            ]);
        }

        return $this->getRequiredList('phpRequirements', $requiredList, $additionalData);
    }

    /**
     * Get required database params
     * @return array
     */
    protected function getDatabaseRequiredList($requiredOnly, array $additionalData = null)
    {
        $databaseTypeName = 'Mysql';

        $databaseHelper =  $this->getDatabaseHelper();
        $databaseParams = isset($additionalData['database']) ? $additionalData['database'] : null;
        $dbalConnection = $databaseHelper->createDbalConnection($databaseParams);
        if ($dbalConnection) {
            $databaseHelper->setDbalConnection($dbalConnection);
            $databaseType = $databaseHelper->getDatabaseType();
            $databaseTypeName = ucfirst(strtolower($databaseType));
        }

        $requiredList = [
            'required' . $databaseTypeName . 'Version',
        ];

        if (!$requiredOnly) {
            $requiredList = array_merge($requiredList, [
                'recommended' . $databaseTypeName . 'Params',
                'connection',
            ]);
        }

        return $this->getRequiredList('databaseRequirements', $requiredList, $additionalData);
    }

    /**
     * Get permission requirements
     * @return array
     */
    protected function getRequiredPermissionList($requiredOnly, array $additionalData = null)
    {
        return $this->getRequiredList('permissionRequirements', [
            'permissionMap.writable',
            'permissionMap.readable',
        ], $additionalData);
    }

    protected function getRequiredList($type, $checkList, array $additionalData = null)
    {
        $config = $this->getConfig();

        $list = [];

        foreach ($checkList as $itemName) {
            $methodName = 'check' . ucfirst($type);
            if (method_exists($this, $methodName)) {
                $result = $this->$methodName($itemName, $config->get($itemName), $additionalData);
                $list = array_merge($list, $result);
            }
        }

        return $list;
    }

    /**
     * Check php requirements
     * @param  string $type
     * @param  mixed $data
     * @return array
     */
    protected function checkPhpRequirements($type, $data, array $additionalData = null)
    {
        $list = [];

        switch ($type) {
            case 'requiredPhpVersion':
                $actualVersion = $this->getSystemHelper()->getPhpVersion();
                $requiredVersion = $data;

                $acceptable = true;
                if (version_compare($actualVersion, $requiredVersion) == -1) {
                    $acceptable = false;
                }

                $list[$type] = [
                    'type' => 'version',
                    'acceptable' => $acceptable,
                    'required' => $requiredVersion,
                    'actual' => $actualVersion,
                ];
                break;

            case 'requiredPhpLibs':
            case 'recommendedPhpLibs':
                foreach ($data as $name) {
                    $acceptable = $this->getSystemHelper()->hasPhpLib($name);

                    $list[$name] = array(
                        'type' => 'lib',
                        'acceptable' => $acceptable,
                        'actual' => $acceptable ? 'On' : 'Off',
                    );
                }
                break;

            case 'recommendedPhpParams':
                foreach ($data as $name => $value) {
                    $requiredValue = $value;
                    $actualValue = $this->getSystemHelper()->getPhpParam($name);

                    $acceptable = ( isset($actualValue) && Util::convertToByte($actualValue) >= Util::convertToByte($requiredValue) ) ? true : false;

                    $list[$name] = array(
                        'type' => 'param',
                        'acceptable' => $acceptable,
                        'required' => $requiredValue,
                        'actual' => $actualValue,
                    );
                }
                break;
        }

        return $list;
    }

    /**
     * Check MySQL requirements
     * @param  string $type
     * @param  mixed $data
     * @return array
     */
    protected function checkDatabaseRequirements($type, $data, array $additionalData = null)
    {
        $list = [];

        $databaseHelper = $this->getDatabaseHelper();

        $databaseParams = isset($additionalData['database']) ? $additionalData['database'] : null;
        $pdo = $databaseHelper->createPdoConnection($databaseParams);
        if (!$pdo) {
            $type = 'connection';
        }

        switch ($type) {
            case 'requiredMysqlVersion':
            case 'requiredMariadbVersion':
                $actualVersion = $databaseHelper->getPdoDatabaseVersion($pdo);
                $requiredVersion = $data;

                $acceptable = true;
                if (version_compare($actualVersion, $requiredVersion) == -1) {
                    $acceptable = false;
                }

                $list[$type] = [
                    'type' => 'version',
                    'acceptable' => $acceptable,
                    'required' => $requiredVersion,
                    'actual' => $actualVersion,
                ];
                break;

            case 'recommendedMysqlParams':
            case 'recommendedMariadbParams':
                foreach ($data as $name => $value) {
                    $requiredValue = $value;
                    $actualValue = $databaseHelper->getPdoDatabaseParam($name, $pdo);

                    $acceptable = false;

                    switch (gettype($requiredValue)) {
                        case 'integer':
                            if (Util::convertToByte($actualValue) >= Util::convertToByte($requiredValue)) {
                                $acceptable = true;
                            }
                            break;

                        case 'string':
                            if (strtoupper($actualValue) == strtoupper($requiredValue)) {
                                $acceptable = true;
                            }
                            break;
                    }

                    $list[$name] = array(
                        'type' => 'param',
                        'acceptable' => $acceptable,
                        'required' => $requiredValue,
                        'actual' => $actualValue,
                    );
                }
                break;

            case 'connection':
                    if (!$databaseParams) {
                        $databaseParams = $this->getConfig()->get('database');
                    }

                    $acceptable = true;
                    if (!$pdo instanceof \PDO) {
                        $acceptable = false;
                    }

                    $list['host'] = [
                        'type' => 'connection',
                        'acceptable' => $acceptable,
                        'actual' => $databaseParams['host'],
                    ];
                    $list['dbname'] = [
                        'type' => 'connection',
                        'acceptable' => $acceptable,
                        'actual' => $databaseParams['dbname'],
                    ];
                    $list['user'] = [
                        'type' => 'connection',
                        'acceptable' => $acceptable,
                        'actual' => $databaseParams['user'],
                    ];
                    break;
        }

        return $list;
    }

    protected function checkPermissionRequirements($type, $data, array $additionalData = null)
    {
        $list = [];

        $fileManager = $this->getFileManager();

        switch ($type) {
            case 'permissionMap.writable':
                foreach ($data as $item) {
                    $fullPathItem = Util::concatPath($this->getSystemHelper()->getRootDir(), $item);
                    $list[$fullPathItem] = [
                        'type' => 'writable',
                        'acceptable' => $fileManager->isWritable($fullPathItem) ? true : false,
                    ];
                }
                break;

            case 'permissionMap.readable':
                foreach ($data as $item) {
                    $fullPathItem = Util::concatPath($this->getSystemHelper()->getRootDir(), $item);
                    $list[$fullPathItem] = [
                        'type' => 'readable',
                        'acceptable' => $fileManager->isReadable($fullPathItem) ? true : false,
                    ];
                }
                break;
        }

        return $list;
    }
}
