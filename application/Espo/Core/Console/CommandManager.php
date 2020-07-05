<?php


namespace Espo\Core\Console;

class CommandManager
{
    private $container;

    public function __construct(\Espo\Core\Container $container)
    {
        $this->container = $container;
    }

    public function run(string $command)
    {
        $command = ucfirst(\Espo\Core\Utils\Util::hyphenToCamelCase($command));

        $argumentList = [];
        $options = [];
        $flagList = [];

        $skipIndex = 1;
        if (isset($_SERVER['argv'][0]) && $_SERVER['argv'][0] === 'command.php') {
            $skipIndex = 2;
        }

        foreach ($_SERVER['argv'] as $i => $item) {
            if ($i < $skipIndex) continue;

            if (strpos($item, '--') === 0 && strpos($item, '=') > 2) {
                list($name, $value) = explode('=', substr($item, 2));
                $name = \Espo\Core\Utils\Util::hyphenToCamelCase($name);
                $options[$name] = $value;
            } else if (strpos($item, '-') === 0) {
                $flagList[] = substr($item, 1);
            } else {
                $argumentList[] = $item;
            }
        }

        $className = '\\Espo\\Core\\Console\\Commands\\' . $command;
        $className = $this->container->get('metadata')->get(['app', 'consoleCommands', $command, 'className'], $className);
        if (!class_exists($className)) {
            $msg = "Command '{$command}' does not exist.";
            echo $msg . "\n";
            throw new \Espo\Core\Exceptions\Error($msg);
        }
        $impl = new $className($this->container);
        return $impl->run($options, $flagList, $argumentList);
    }
}
