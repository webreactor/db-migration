<?php

namespace Dbml\CliControllers;

use \Dbml\Utilities;

class LoadController extends BaseController {

    public function handle() {
        $this->initTracker();
        $migrations = $this->app->getAllMigrations();
        $this->checkNonMigrated($migrations);
        
        foreach ($migrations as $id => $migration) {
            if ($migration->status == 'new') {
                $this->printMigration($migration);

                $this->register($migration);

                if (!empty($migration->before)) {
                    $this->setStatus($migration, 'before');
                    Utilities::exec($this->app->parameters['migrations'].$migration->before);
                }

                $this->setStatus($migration, 'loading...');
                $this->app->getTracker()->load($migration);

                if (!empty($migration->after)) {
                    $this->setStatus($migration, 'after');
                    Utilities::exec($this->app->parameters['migrations'].$migration->after);
                }

                $this->setStatus($migration, 'migrated');
                echo "\n";
            }
        }

        echo "All migrated\n";

    }

    public function checkNonMigrated($migrations) {
        $non_migrated = array();
        foreach ($migrations as $key => $migration) {
            if (!in_array($migration->status, array('new', 'migrated'))) {
                $non_migrated[] = $migration;
            }
        }
        if(count($non_migrated) > 0) {
                echo "Error: non migrated migrations:\n";
                foreach ($non_migrated as $key => $migration) {
                    $this->printMigration($migration);
                }
                exit(1);
        }
    }

    public function setStatus($migration, $status) {
        $this->app->getTracker()->setStatus($migration, $status);
        echo "$status ".date('r')."\n";
    }

    public function register($migration) {
        $this->app->getTracker()->register($migration);
        echo "registered ".date('r')."\n";
    }

}
