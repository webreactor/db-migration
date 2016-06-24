<?php

namespace Reactor\DbMigration\CliControllers;

use Reactor\DbMigration\Utilities;
use Reactor\CliArguments\ArgumentDefinition;

class AppController extends BaseController {

    public function handle($request) {
        parent::handle($request);
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
        if (!$this->app->parameters['clean']) {
            $this->welcome();
        }
        $command  = $this->getCommand();
        $c_name = Utilities::strToClassName($command);
        $c_name = 'Reactor\\DbMigration\\CliControllers\\'.$c_name.'Controller';
        if (!class_exists($c_name)) {
            throw new \Exception("No such command '$command'", 1);
        }
        $command_ctrl = new $c_name($this->app);
        $command_ctrl->handle($this->request);
    }

    public function getCommand() {
        $this->request->addDefinition(new ArgumentDefinition('_words_', '', '', false, true));
        $this->request->parse();
        $words = $this->request->get('_words_');
        if (!isset($words[1])) {
            $words[1] = 'help';
        }
        $command = $words[1];
        $this->app->setParameters(array(
            'command' => $command,
            '_words_' => $words,
        ));
        return $command;
    }

    public function loadCommonParameters() {
        $this->request->addDefinition(new ArgumentDefinition('clean', '', true, false, false, 'clean output'));
        $this->request->parse();
        $clean = $this->request->get('clean');
        $this->app->setParameters(array('clean' => ($clean !== 'no')));
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