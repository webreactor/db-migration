<?php

namespace Reactor\DbMigration\CliControllers;

class AllMigratedController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();
        $migrations = $this->app->getTrackedMigrations();
        $tracker = $this->app->getTracker();
        $cnt = 0;
        foreach ($migrations as $migration) {
            $this->printMigration($migration);
            if ($migration->status == 'new') {
                $tracker->register($migration);
                $tracker->setStatus($migration, 'migrated');
                echo "Marked as migrated\n";
            }
            $cnt++;
        }
        echo "All migrated\n";
    }

}
