<?php


namespace Espo\Core\Portal;

class Container extends \Espo\Core\Container
{
    public function getServiceClassName(string $name, string $default)
    {
        $metadata = $this->get('metadata');
        $className = $metadata->get(['app', 'serviceContainerPortal', 'classNames',  $name], $default);
        return $className;
    }

    protected function getServiceMainClassName(string $name, string $default)
    {
        $metadata = $this->get('metadata');
        $className = $metadata->get(['app', 'serviceContainer', 'classNames',  $name], $default);
        return $className;
    }

    protected function loadAclManager()
    {
        $className = $this->getServiceClassName('aclManager', '\\Espo\\Core\\Portal\\AclManager');
        $mainClassName = $this->getServiceMainClassName('aclManager', '\\Espo\\Core\\AclManager');

        $obj = new $className(
            $this->get('container')
        );
        $objMain = new $mainClassName(
            $this->get('container')
        );
        $obj->setMainManager($objMain);

        return $obj;
    }

    protected function loadAcl()
    {
        $className = $this->getServiceClassName('acl', '\\Espo\\Core\\Portal\\Acl');
        return new $className(
            $this->get('aclManager'),
            $this->get('user')
        );
    }

    protected function loadThemeManager()
    {
        return new \Espo\Core\Portal\Utils\ThemeManager(
            $this->get('config'),
            $this->get('metadata'),
            $this->get('portal')
        );
    }

    protected function loadLayout()
    {
        return new \Espo\Core\Portal\Utils\Layout(
            $this->get('fileManager'),
            $this->get('metadata'),
            $this->get('user')
        );
    }

    protected function loadLanguage()
    {
        $language = new \Espo\Core\Portal\Utils\Language(
            \Espo\Core\Utils\Language::detectLanguage($this->get('config'), $this->get('preferences')),
            $this->get('fileManager'),
            $this->get('metadata'),
            $this->get('useCache')
        );
        $language->setPortal($this->get('portal'));
        return $language;
    }

    public function setPortal(\Espo\Entities\Portal $portal)
    {
        $this->set('portal', $portal);

        $data = [];
        foreach ($this->get('portal')->getSettingsAttributeList() as $attribute) {
            $data[$attribute] = $this->get('portal')->get($attribute);
        }
        if (empty($data['language'])) {
            unset($data['language']);
        }
        if (empty($data['theme'])) {
            unset($data['theme']);
        }
        if (empty($data['timeZone'])) {
            unset($data['timeZone']);
        }
        if (empty($data['dateFormat'])) {
            unset($data['dateFormat']);
        }
        if (empty($data['timeFormat'])) {
            unset($data['timeFormat']);
        }
        if (isset($data['weekStart']) && $data['weekStart'] === -1) {
            unset($data['weekStart']);
        }
        if (array_key_exists('weekStart', $data) && is_null($data['weekStart'])) {
            unset($data['weekStart']);
        }
        if (empty($data['defaultCurrency'])) {
            unset($data['defaultCurrency']);
        }

        if ($this->get('config')->get('webSocketInPortalDisabled')) {
            $this->get('config')->set('useWebSocket', false);
        }

        foreach ($data as $attribute => $value) {
            $this->get('config')->set($attribute, $value, true);
        }
    }
}
