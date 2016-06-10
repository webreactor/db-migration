<?php

namespace Dbml\CliControllers;

use Dbml\Utilities;

class DbmlController extends BaseController {

    public function handle() {
        try {
            $this->testDependencies();
            $this->handleBody();
        } catch (\Exception $e) {
            echo "Error: ".$e->getMessage()."\n";
            exit(1);
        }
    }

    public function handleBody() {
        $this->loadCommonParameters();
        if (!isset($this->app->parameters['clean'])) {
            $this->welcome();
        }
        $command = Utilities::strToClassName($this->getCommand());
        $command = 'Dbml\\CliControllers\\'.$command.'Controller';
        $command_ctrl = new $command($this->app);
        $command_ctrl->handle();
    }

    public function getCommand() {
        $options = getopt('', array(
            'init',
            'load',
            'list::',
            'new',
            'create::',
            'reset::',
            'reset-locks',
        ));
        if (count($options) == 0) {
            return 'help';
        }
        $this->app->setParameters($options);
        $options = array_keys($options);
        return $options[0];
    }

    public function loadCommonParameters() {
        $options = getopt('', array(
            'clean',
        ));
        $this->app->setParameters($options);
    }

    public function welcome() {
        echo "db-migration - Database migration scripts loader. Version ".$this->app->parameters['app-version']."\n\n";
    }

    public function testDependencies() {
        if (!class_exists('PDO')) {
            throw new \Exception("Missing PDO driver.\nThis might help: sudo apt-get install php5-mysql\n", 1);
        }
        $a = exec('mysql -V', $out, $code);
        if ($code > 0) {
            throw new \Exception("Missing mysql client tool.\nThis might help: sudo apt-get install mysql-client\n", 1);
        }
    }

}