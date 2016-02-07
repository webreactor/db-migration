<?php

namespace Dbml\CliControllers;

class ResetLocksController extends BaseController {

    public function handle() {
        $this->initTracker();
        $mirations = $this->app->getAllMigrations();
        $tracker = $this->app->getTracker();
        $cnt = 0;
        foreach ($mirations as $migration) {
            if (!in_array($migration->status,array('new', 'migrated'))) {
                $this->printMigration($migration);
                $tracker->delete($migration);
                $cnt++;
            }
        }
        echo "All unlocked\n";
    }
}
