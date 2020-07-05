<?php


namespace Espo\Core\Utils;

use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Util;

class AdminNotificationManager
{
    private $container;

    public function __construct(\Espo\Core\Container $container)
    {
        $this->container = $container;
    }

    protected function getContainer()
    {
        return $this->container;
    }

    protected function getEntityManager()
    {
        return $this->getContainer()->get('entityManager');
    }

    protected function getConfig()
    {
        return $this->getContainer()->get('config');
    }

    protected function getLanguage()
    {
        return $this->getContainer()->get('language');
    }

    public function getNotificationList()
    {
        $notificationList = [];

        if (!$this->getConfig()->get('adminNotifications')) {
            return [];
        }

        if ($this->getConfig()->get('adminNotificationsCronIsNotConfigured')) {
            if (!$this->isCronConfigured()) {
                $notificationList[] = array(
                    'id' => 'cronIsNotConfigured',
                    'type' => 'cronIsNotConfigured',
                    'message' => $this->getLanguage()->translate('cronIsNotConfigured', 'messages', 'Admin')
                );
            }
        }

        if ($this->getConfig()->get('adminNotificationsNewVersion')) {
            $instanceNeedingUpgrade = $this->getInstanceNeedingUpgrade();
            if (!empty($instanceNeedingUpgrade)) {
                $message = $this->getLanguage()->translate('newVersionIsAvailable', 'messages', 'Admin');
                $notificationList[] = array(
                    'id' => 'newVersionIsAvailable',
                    'type' => 'newVersionIsAvailable',
                    'message' => $this->prepareMessage($message, $instanceNeedingUpgrade)
                );
            }
        }

        if ($this->getConfig()->get('adminNotificationsNewExtensionVersion')) {
            $extensionsNeedingUpgrade = $this->getExtensionsNeedingUpgrade();
            if (!empty($extensionsNeedingUpgrade)) {
                foreach ($extensionsNeedingUpgrade as $extensionName => $extensionDetails) {
                    $label = 'new' . Util::toCamelCase($extensionName, ' ', true) .'VersionIsAvailable';

                    $message = $this->getLanguage()->get(['Admin', 'messages', $label]);
                    if (!$message) {
                        $message = $this->getLanguage()->translate('newExtensionVersionIsAvailable', 'messages', 'Admin');
                    }

                    $notificationList[] = array(
                        'id' => 'newExtensionVersionIsAvailable' . Util::toCamelCase($extensionName, ' ', true),
                        'type' => 'newExtensionVersionIsAvailable',
                        'message' => $this->prepareMessage($message, $extensionDetails)
                    );
                }
            }
        }

        return $notificationList;
    }

    protected function isCronConfigured()
    {
        return $this->getContainer()->get('scheduledJob')->isCronConfigured();
    }

    protected function getInstanceNeedingUpgrade()
    {
        $config = $this->getConfig();

        $latestVersion = $config->get('latestVersion');
        if (isset($latestVersion)) {
            $currentVersion = $config->get('version');
            if ($currentVersion === 'dev') return;
            if (version_compare($latestVersion, $currentVersion, '>')) {
                return array(
                    'currentVersion' => $currentVersion,
                    'latestVersion' => $latestVersion
                );
            }
        }
    }

    protected function getExtensionsNeedingUpgrade()
    {
        $config = $this->getConfig();

        $extensions = [];

        $latestExtensionVersions = $config->get('latestExtensionVersions');
        if (!empty($latestExtensionVersions) && is_array($latestExtensionVersions)) {
            foreach ($latestExtensionVersions as $extensionName => $extensionLatestVersion) {
                $currentVersion = $this->getExtensionLatestInstalledVersion($extensionName);
                if (isset($currentVersion) && version_compare($extensionLatestVersion, $currentVersion, '>')) {
                    $extensions[$extensionName] = array(
                        'currentVersion' => $currentVersion,
                        'latestVersion' => $extensionLatestVersion,
                        'extensionName' => $extensionName,
                    );
                }
            }
        }

        return $extensions;
    }

    protected function getExtensionLatestInstalledVersion($extensionName)
    {
        $pdo = $this->getEntityManager()->getPDO();

        $query = "
            SELECT version FROM extension
            WHERE name = ". $pdo->quote($extensionName) ."
            AND deleted = 0
            AND is_installed = 1
            ORDER BY created_at DESC
        ";
        $sth = $pdo->prepare($query);
        $sth->execute();

        $row = $sth->fetch(\PDO::FETCH_ASSOC);
        if (isset($row['version'])) {
            return $row['version'];
        }
    }

    /**
     * Create EspoCRM notification for a user
     *
     * @param  string $message
     * @param  string $userId
     *
     * @return void
     */
    public function createNotification($message, $userId = '1')
    {
        $notification = $this->getEntityManager()->getEntity('Notification');
        $notification->set(array(
            'type' => 'message',
            'data' => array(
                'userId' => $userId,
            ),
            'userId' => $userId,
            'message' => $message
        ));
        $this->getEntityManager()->saveEntity($notification);
    }

    protected function prepareMessage($message, array $data = array())
    {
        foreach ($data as $name => $value) {
            $message = str_replace('{'.$name.'}', $value, $message);
        }

        return $message;
    }
}
