<?php

namespace Reactor\DbMigration\CliControllers;

use Reactor\DbMigration\Utilities;

class LoadController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();
        $migrations = $this->app->getTrackedMigrations();
        $this->checkNonMigrated($migrations);
        
        foreach ($migrations as $id => $migration) {
            if ($migration->status == 'new') {
                $this->printMigration($migration);

                $this->register($migration);

                if (!empty($migration->before)) {
                    $this->setStatus($migration, 'before');
                    Utilities::exec($migration->before);
                }

                $this->setStatus($migration, 'loading...');
                $this->app->getTracker()->load($migration);

                if (!empty($migration->after)) {
                    $this->setStatus($migration, 'after');
                    Utilities::exec($migration->after);
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
