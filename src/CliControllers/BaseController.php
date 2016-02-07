<?php

namespace Dbml\CliControllers;

use \Dbml\Utilities;

class BaseController {

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
        $defaults = array(
            'config'        => 'db-migration.yml',
            'migrations'    => getcwd(),
            'driver'        => 'mysql',
        );
        $cli_options = $this->getCliArguments($defaults);

        $config_file = $defaults['config'];
        if (isset($cli_options['config'])) {
            $config_file = $cli_options['config'];
        }
        
        $config_file = getcwd().DIRECTORY_SEPARATOR.$config_file;
        
        $file_options = array();
        if (is_file($config_file)) {
            $file_options = Utilities::loadConfig($config_file);
            chdir(dirname($config_file));
        }
        $parameters = array_merge($defaults, $file_options, $cli_options);
        $this->app->setParameters($parameters);

        $tracker_defaults = $this->app->getTrackerDefaults();
        $tracker_parameters = $this->getCliArguments($tracker_defaults);

        $parameters = array_merge($defaults, $tracker_defaults, $file_options, $cli_options, $tracker_parameters);
        $parameters['migrations'] = realpath($parameters['migrations']).DIRECTORY_SEPARATOR;
        $this->app->setParameters($parameters);
    }

    public function printParameters() {
        $dumper = new YmlDumper();
        echo "Current parameters:\n";
        $parameters = $this->app->parameters;
        if (!empty($parameters['password'])) {
            $parameters['password'] = 'xxxxxx';
        }
        echo $dumper->dump($parameters, 10, 2);
        echo "\n";
    }

}
