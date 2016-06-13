<?php

namespace Dbml\CliControllers;

class ResetLocksController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();
        $migrations = $this->app->getAllMigrations();
        $tracker = $this->app->getTracker();
        $cnt = 0;
        foreach ($migrations as $migration) {
            if (!in_array($migration->status,array('new', 'migrated'))) {
                $this->printMigration($migration);
                $tracker->delete($migration);
                $cnt++;
            }
        }
        echo "All unlocked\n";
    }
}
