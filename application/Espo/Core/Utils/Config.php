<?php


namespace Espo\Core\Utils;

class Config
{
    private $defaultConfigPath = 'application/Espo/Core/defaults/config.php';

    private $systemConfigPath = 'application/Espo/Core/defaults/systemConfig.php';

    protected $configPath = 'data/config.php';

    private $cacheTimestamp = 'cacheTimestamp';

    protected $adminItems = [];

    protected $associativeArrayAttributeList = [
        'currencyRates',
        'database',
        'logger',
        'defaultPermissions',
    ];


    private $data;

    private $changedData = [];

    private $removeData = [];

    private $fileManager;


    public function __construct(\Espo\Core\Utils\File\Manager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    protected function getFileManager()
    {
        return $this->fileManager;
    }

    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * Get an option from config
     *
     * @param string $name
     * @param string $default
     * @return string | array
     */
    public function get($name, $default = null)
    {
        $keys = explode('.', $name);

        $lastBranch = $this->loadConfig();
        foreach ($keys as $keyName) {
            if (isset($lastBranch[$keyName]) && (is_array($lastBranch) || is_object($lastBranch))) {
                if (is_array($lastBranch)) {
                    $lastBranch = $lastBranch[$keyName];
                } else {
                    $lastBranch = $lastBranch->$keyName;
                }
            } else {
                return $default;
            }
        }

        return $lastBranch;
    }

    /**
     * Whether parameter is set
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        $keys = explode('.', $name);

        $lastBranch = $this->loadConfig();
        foreach ($keys as $keyName) {
            if (isset($lastBranch[$keyName]) && (is_array($lastBranch) || is_object($lastBranch))) {
                if (is_array($lastBranch)) {
                    $lastBranch = $lastBranch[$keyName];
                } else {
                    $lastBranch = $lastBranch->$keyName;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Set an option to the config
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function set($name, $value = null, $dontMarkDirty = false)
    {
        if (is_object($name)) {
            $name = get_object_vars($name);
        }

        if (!is_array($name)) {
            $name = array($name => $value);
        }

        foreach ($name as $key => $value) {
            if (in_array($key, $this->associativeArrayAttributeList) && is_object($value)) {
                $value = (array) $value;
            }
            $this->data[$key] = $value;
            if (!$dontMarkDirty) {
                $this->changedData[$key] = $value;
            }
        }
    }

    /**
     * Remove an option in config
     *
     * @param  string $name
     * @return bool | null - null if an option doesn't exist
     */
    public function remove($name)
    {
        if (array_key_exists($name, $this->data)) {
            unset($this->data[$name]);
            $this->removeData[] = $name;
            return true;
        }

        return null;
    }

    public function save()
    {
        $values = $this->changedData;

        if (!isset($values[$this->cacheTimestamp])) {
            $values = array_merge($this->updateCacheTimestamp(true), $values);
        }

        $removeData = empty($this->removeData) ? null : $this->removeData;

        $data = include($this->configPath);

        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $data[$key] = $value;
            }
        }

        if (is_array($removeData)) {
            foreach ($removeData as $key) {
                unset($data[$key]);
            }
        }

        $result = $this->getFileManager()->putPhpContents($this->configPath, $data, true);

        if ($result) {
            $this->changedData = array();
            $this->removeData = array();
            $this->loadConfig(true);
        }

        return $result;
    }

    public function getDefaults()
    {
        return $this->getFileManager()->getPhpContents($this->defaultConfigPath);
    }

    protected function loadConfig($reload = false)
    {
        if (!$reload && isset($this->data) && !empty($this->data)) {
            return $this->data;
        }

        $configPath = file_exists($this->configPath) ? $this->configPath : $this->defaultConfigPath;

        $this->data = $this->getFileManager()->getPhpContents($configPath);

        $systemConfig = $this->getFileManager()->getPhpContents($this->systemConfigPath);
        $this->data = Util::merge($systemConfig, $this->data);

        return $this->data;
    }

    public function getAllData()
    {
        return (object) $this->loadConfig();
    }

    public function getData($isAdmin = null)
    {
        $data = $this->loadConfig();

        return $data;
    }

    public function setData($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        return $this->set($data);
    }

    /**
     * Update cache timestamp
     *
     * @param $onlyValue - If need to return just timestamp array
     * @return bool | array
     */
    public function updateCacheTimestamp($onlyValue = false)
    {
        $timestamp = [
            $this->cacheTimestamp => time()
        ];

        if ($onlyValue) {
            return $timestamp;
        }

        return $this->set($timestamp);
    }

    public function getAdminOnlyItemList()
    {
        return $this->get('adminItems', []);
    }

    public function getSuperAdminOnlyItemList()
    {
        return $this->get('superAdminItems', []);
    }

    public function getSystemOnlyItemList()
    {
        return $this->get('systemItems', []);
    }

    public function getSuperAdminOnlySystemItemList()
    {
        return $this->get('superAdminSystemItems', []);
    }

    public function getUserOnlyItemList()
    {
        return $this->get('userItems', []);
    }

    public function getSiteUrl()
    {
        return rtrim($this->get('siteUrl'), '/');
    }
}
