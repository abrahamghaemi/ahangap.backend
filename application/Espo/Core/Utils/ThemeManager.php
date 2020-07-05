<?php


namespace Espo\Core\Utils;

class ThemeManager
{
    protected $config;

    protected $metadata;

    protected $defaultName = 'Espo';

    private $defaultStylesheet = 'Espo';

    public function __construct(Config $config, Metadata $metadata)
    {
        $this->config = $config;
        $this->metadata = $metadata;
    }

    public function getName()
    {
        return $this->config->get('theme', $this->defaultName);
    }

    public function getStylesheet()
    {
        return $this->metadata->get('themes.' . $this->getName() . '.stylesheet', 'client/css/espo.css');
    }
}


