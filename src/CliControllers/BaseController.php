<?php

namespace Dbml\CliControllers;

use \Dbml\Utilities;

class BaseController {
    /**
     * @var \Dbml\Application
     */
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
            $migration->fullname,
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
            $this->request->addDefinition($name, $definition[1], $definition[2]);
        }
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
            if (isset($file_options['migrations']) && !isset($cli_options['migrations'])) {
                chdir(dirname($config_file));
            }
        }
        $parameters = array_merge($file_options, $config_cli_parameters);
        $this->app->setParameters($parameters);

        $base_config = array(
            array('migrations', 'm', 'db-migrations'),
            array('driver', 'r', 'mysql'),
        );

        $parameters = $this->getCliArguments($base_config, $parameters);
        $this->app->setParameters($parameters);

        $tracker_defaults = $this->app->getTrackerDefaults();
        $parameters = $this->getCliArguments($tracker_defaults, $parameters);

        $real = realpath($parameters['migrations']);
        if ($real === false) {
            throw new \Exception("Cannot find migrations path '{$parameters['migrations']}'", 1);
        }
        $parameters['migrations'] = $real.DIRECTORY_SEPARATOR;
        $this->app->setParameters($parameters);
        // if (!($this->app->parameters['clean'] !== 'no')) {
        //     $this->printParameters();
        // }
        if (empty($this->app->parameters['database'])) {
            throw new \Exception("Missing database name");
        }
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
