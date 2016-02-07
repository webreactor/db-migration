<?php

namespace Reactor\Database;

use Reactor\Application\Exceptions\ModuleConfiguratorException;

class Module extends \Reactor\Application\Module {
    public function configure($container, $config = array()) {
        $configurator = parent::configure($container, $config);

        foreach ($this->get('connections') as $key => $value) {
            $this->createService($key, '\\Reactor\\Database\\PDO\\Connection', array($value['link'], $value['user'], $value['password']));
        }
    }
}
