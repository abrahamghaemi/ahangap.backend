<?php


namespace Espo\Core\Utils;

class ClientManager
{
    private $themeManager;

    private $config;

    private $metadata;

    protected $mainHtmlFilePath = 'html/main.html';

    protected $runScript = "app.start();";

    protected $basePath = '';

    public function __construct(Config $config, ThemeManager $themeManager, Metadata $metadata)
    {
        $this->config = $config;
        $this->themeManager = $themeManager;
        $this->metadata = $metadata;
    }

    protected function getThemeManager()
    {
        return $this->themeManager;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    protected function getCacheTimestamp()
    {
        if (!$this->getConfig()->get('useCache')) {
            return (string) time();
        }
        return $this->getConfig()->get('cacheTimestamp', 0);
    }

    public function display($runScript = null, $htmlFilePath = null, $vars = [])
    {
        if (is_null($runScript)) {
            $runScript = $this->runScript;
        }
        if (is_null($htmlFilePath)) {
            $htmlFilePath = $this->mainHtmlFilePath;
        }

        $isDeveloperMode = $this->getConfig()->get('isDeveloperMode');

        $cacheTimestamp = $this->getCacheTimestamp();

        if ($isDeveloperMode) {
            $useCache = $this->getConfig()->get('useCacheInDeveloperMode');
            $jsFileList = $this->getMetadata()->get(['app', 'client', 'developerModeScriptList']);
            $loaderCacheTimestamp = 'null';
        } else {
            $useCache = $this->getConfig()->get('useCache');
            $jsFileList = $this->getMetadata()->get(['app', 'client', 'scriptList']);
            $loaderCacheTimestamp = $cacheTimestamp;
        }

        $scriptsHtml = '';
        foreach ($jsFileList as $jsFile) {
            $src = $this->basePath . $jsFile . '?r=' . $cacheTimestamp;
            $scriptsHtml .= '        ' .
            '<script type="text/javascript" src="'.$src.'" data-base-path="'.$this->basePath.'"></script>' . "\n";
        }

        $data = [
            'applicationId' => 'snapp-application-id',
            'apiUrl' => 'api/v1',
            'applicationName' => $this->getConfig()->get('applicationName', 'Snapp CRM'),
            'cacheTimestamp' => $cacheTimestamp,
            'loaderCacheTimestamp' => $loaderCacheTimestamp,
            'stylesheet' => $this->getThemeManager()->getStylesheet(),
            'runScript' => $runScript,
            'basePath' => $this->basePath,
            'useCache' => $useCache ? 'true' : 'false',
            'appClientClassName' => 'app',
            'scriptsHtml' => $scriptsHtml
        ];

        $html = file_get_contents($htmlFilePath);

        foreach ($vars as $key => $value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $vars)) continue;
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }

        echo $html;
    }
}
