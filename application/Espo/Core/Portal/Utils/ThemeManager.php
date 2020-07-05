<?php


namespace Espo\Core\Portal\Utils;

use \Espo\Entities\Portal;

use \Espo\Core\Utils\Config;
use \Espo\Core\Utils\Metadata;

class ThemeManager extends \Espo\Core\Utils\ThemeManager
{
    public function __construct(Config $config, Metadata $metadata, Portal $portal)
    {
        $this->config = $config;
        $this->metadata = $metadata;
        $this->portal = $portal;
    }

    public function getName()
    {
        $theme = $this->portal->get('theme');
        if (!$theme) {
            $theme = $this->config->get('theme', $this->defaultName);
        }
        return $theme;
    }
}


