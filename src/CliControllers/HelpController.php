<?php

namespace Dbml\CliControllers;


class HelpController {

    public function __construct($app) {
        $this->app = $app;
    }

    public function handle() {
        echo "Usage:\n";
        echo "  dbml <command> [options] [migration options]\n";
        echo "\nCommands:\n";
        echo "  --init          Create config YML file\n";
        echo "  --load          Load new migrations\n";
        echo "  --list          List all loaded and new migration files\n";
        echo "  --list={limit}  List last {limit} loaded and new migration files\n";
        echo "  --new           List of new migrations\n";
        echo "  --create={name} Create new empty migration file\n";
        echo "  --reset={id}    Reset migration state\n";
        echo "  --reset-locked  Reset all migration with state no new or migrated\n";
        echo "  --help\n";

        echo "\nOptions:\n";
        echo "  --clean         Clean output, no headers\n";
        echo "  --config        Path to config YML file. Default db-migration.yml at current folder\n";

        echo "\nMigration Options:\n";
        echo "  --migrations    Path to migration scripts\n";
        echo "  --host          Default is localhost\n";
        echo "  --port          Default is 3306\n";
        echo "  --unix_socket   Path to socket. has more priority that host\n";
        echo "  --user\n";
        echo "  --password\n";
        echo "  --database          Database name\n";
        echo "  --create-database   Creates db if not exists\n";
        echo "  --table             Table name for migration state. Default is db-migrations\n";
        echo "  --extra             Extra parameters will bepassed to load a migration command\n";

        echo "\nAll options can be specified in YML file\n";

    }

}
