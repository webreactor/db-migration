<?php

namespace Dbml\CliControllers;

use Dbml\Utilities;

class HelpController extends BaseController {

    public function getAvaialbeCommands() {
        return array(
            'init'          => 'Create config YML file',
            'load'          => 'Load new migrations',
            'list'          => 'List all loaded and new migration files',
            'list {limit}'  => 'List last {limit} loaded and new migration files',
            'new'           => 'List of new migrations',
            'create {name}' => 'Create new empty migration file',
            'reset {id}'    => 'Reset migration state',
            'reset-locks'   => 'Reset all migration with state no new or migrated',
            'config'        => 'Show current config',
            'help'          => 'Show help',
        );
    }
    public function getAvaialbeDrivers() {
        return array(
            'mysql',
        );
    }

    public function handle($request) {
        echo "Usage:\n";
        echo "  dbml <command> [--option value]\n";
        $commands = $this->getAvaialbeCommands();
        echo "\nCommands:\n";
        foreach ($commands as $key => $value) {
            echo sprintf("  %-15s %s\n", $key, $value);
        }

        echo "\nOptions:\n";
        echo "  Full name        | Short | Default          | Note\n";
        echo "-----------------------------------------------------\n";
        echo "  --clean                    no                 (yes|no) Clean output, no headers\n";
        echo "  --config           -f      db-migration.yml   Path to config YML file. Default  at current folder\n";
        echo "  --migrations       -m      migrations         Path to migration scripts\n";
        echo "  --driver           -r      mysql              Database driver\n";

        $drivers = $this->getAvaialbeDrivers();
        foreach ($drivers as $driver_name) {
            $c_name = Utilities::strToClassName($driver_name);
            $c_name = 'Dbml\\Drivers\\'.$c_name.'Driver';
            $driver = new $c_name();
            $argumets = $driver->getDefaults();
            echo "\n'$driver_name' driver:\n";
        echo "  Full name        | Short | Default          | Note\n";
        echo "-----------------------------------------------------\n";
            foreach ($argumets as $value) {
                echo sprintf("  --%-16s -%-6s %-18s %s\n", $value[0], $value[1], $value[2], $value[3]);
            }
        }
        echo "\nAll options can be specified in YML file. Pleae use full name of option.\n\n";

    }

}
