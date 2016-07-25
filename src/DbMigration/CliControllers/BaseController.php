<?php

namespace Reactor\DbMigration\CliControllers;

use Reactor\DbMigration\Utilities;
use Reactor\CliArguments\ArgumentDefinition;

class BaseController {
    public $app;
    public $request;

    public function __construct($app) {
        $this->app = $app;
    }

    public function handle($request) {
        $this->request = $request;
    }

    public function initTracker() {
        $this->loadMigrationParameters();
        $this->app->getTracker()->init($this->app->parameters);
    }

    public function printMigration($migration) {
        echo sprintf(
                '%-15s %-10s %-21s',
                $migration->id,
                $migration->status,
                $migration->created
            ),
            $migration->title,
            '  ', $migration->before,
            '  ', $migration->after."\n";
    }

    public function getCliArguments($definitions, $defaults = array()) {
        $this->request->reset();
        foreach ($definitions as $definition) {
            $name = $definition[0];
            if (isset($defaults[$name])) {
                $definition[2] = $defaults[$name];
            }
            if (!empty($definition[4])) {
                $this->request->addDefinition(new ArgumentDefinition($name, $definition[1], $definition[2], false, true));
            } else {
                $this->request->addDefinition(new ArgumentDefinition($name, $definition[1], $definition[2], false, false));
            }
        }
        $this->request->parse();
        return array_merge($defaults, $this->request->getAll());
    }

    public function loadMigrationParameters() {
        $config_file_config = array(
            array('config', 'f', 'db-migration.yml'),
        );
        $config_cli_parameters = $this->getCliArguments($config_file_config);
        
        $config_file = $config_cli_parameters['config'] = realpath($config_cli_parameters['config']);
        
        $file_options = array();
        if (is_file($config_file)) {
            $file_options = Utilities::loadConfig($config_file);
            if (!$file_options) {
                $file_options = array();
            }
            chdir(dirname($config_file));
        }
        $parameters = array_merge($file_options, $config_cli_parameters);
        $this->app->setParameters($parameters);

        $base_config = array(
            array('migrations', 'm', 'db-migrations', "Path to migration scripts", true),
            array('driver', 'r', 'mysql', "Database driver"),
        );

        $parameters = $this->getCliArguments($base_config, $parameters);
        $this->app->setParameters($parameters);

        $tracker_defaults = $this->app->getTrackerDefaults();
        $parameters = $this->getCliArguments($tracker_defaults, $parameters);
        $parameters['migrations'] = $this->normalizeMigrationPaths($parameters['migrations']);

        $parameters['pwd'] = getcwd().DIRECTORY_SEPARATOR;

        $this->app->setParameters($parameters);
        // if (!($this->app->parameters['clean'] !== 'no')) {
        //     $this->printParameters();
        // }
    }

    public function normalizeMigrationPaths($paths) {
        $paths = $this->normalizeMigrationPaths_r($paths);
        $paths = array_map('trim', $paths);
        foreach ($paths as $key => $path) {
            $paths[$key] = rtrim($path, '/\\').DIRECTORY_SEPARATOR;
        }
        return $paths;
    }

    public function normalizeMigrationPaths_r($paths) {
        if (!is_array($paths)) {
            $paths = explode(':', $paths);
            return $paths;
        }
        $rez = array();
        foreach ($paths as $path) {
            $rez = array_merge($rez, $this->normalizeMigrationPaths($path));
        }
        return $rez;
    }

    public function printParameters() {
        echo "Current parameters:\n";
        $parameters = $this->app->parameters;
        if (!empty($parameters['password'])) {
            $parameters['password'] = 'xxxxxx';
        }
        echo Utilities::dumpYAML($parameters, 10, 2);
        echo "\n";
    }

}
