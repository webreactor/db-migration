<?php

namespace Dbml\CliControllers;

use Dbml\Utilities;

class DbmlController extends BaseController {

    public function handle() {
        try {
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
            'load',
            'list',
            'new',
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
        echo "DBML - DataBase Migration scripts Loader\n\n";
    }

}