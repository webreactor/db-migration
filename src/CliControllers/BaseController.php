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

    public function getCliArguments($definitions) {
        foreach ($definitions as $def) {
            $this->request->setDefinition($def[0], $def[1], $def[2]);
        }

        return $this->request->getAll();
    }

    public function loadMigrationParameters() {
        $base_config = array(
            array('config', 'f', 'db-migration.yml'),
            array('migrations', 'm', 'db-migrations'),
            array('driver', 'r', 'mysql'),
        );
        $cli_options = $this->getCliArguments($base_config);
        $config_file = $cli_options['config'];
        
        $config_file = $cli_options['config'] = realpath($config_file);
        
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

        $parameters = array_merge($file_options, $cli_options);
        $this->app->setParameters($parameters);

        $tracker_defaults = $this->app->getTrackerDefaults();
        $tracker_cli_parameters = $this->getCliArguments($tracker_defaults);

        $parameters = array_merge($parameters, $tracker_cli_parameters);
        $real = realpath($parameters['migrations']);
        if ($real === false) {
            throw new \Exception("Cannot find migrations path '{$parameters['migrations']}'", 1);
        }
        $parameters['migrations'] = $real.DIRECTORY_SEPARATOR;
        $this->app->setParameters($parameters);
        if (!isset($this->app->parameters['clean'])) {
            $this->printParameters();
        }
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
