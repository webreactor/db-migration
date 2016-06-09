<?php

namespace Dbml\CliControllers;

use \Dbml\Utilities;

class BaseController {
    /**
     * @var \Dbml\Application
     */
    public $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function handle() {
        
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

    public function getCliArguments($expecting) {
        $names = array();
        foreach ($expecting as $key => $value) {
            $names[] = "$key::";
        }
        return getopt(null, $names);
    }

    public function loadMigrationParameters() {
        $base_config = array(
            'config'        => 'db-migration.yml',
            'migrations'    => 'db-migrations',
            'driver'        => 'mysql',
        );
        $cli_options = $this->getCliArguments($base_config);

        $config_file = $base_config['config'];
        if (isset($cli_options['config'])) {
            $config_file = $cli_options['config'];
        }
        
        $config_file = $cli_options['config'] = realpath($config_file);
        
        if (is_file($config_file)) {
            $file_options = Utilities::loadConfig($config_file);
            if (isset($file_options['migrations']) && !isset($cli_options['migrations'])) {
                chdir(dirname($config_file));
            }
        }

        if (!$file_options) {
            $file_options = array();
        }

        $parameters = array_merge($base_config, $file_options, $cli_options);
        $this->app->setParameters($parameters);

        $tracker_defaults = $this->app->getTrackerDefaults();
        $tracker_cli_parameters = $this->getCliArguments($tracker_defaults);

        $parameters = array_merge($tracker_defaults, $parameters, $tracker_cli_parameters);
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
