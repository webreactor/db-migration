<?php

namespace Dbml\CliControllers;

class NewController extends BaseController {

    public function handle() {
        $this->initTracker();
        $migrations = $this->app->getAllMigrations();

        foreach ($migrations as $migration) {
            if ($migration->status == 'new') {
                $this->printMigration($migration);
            }
        }
    }
}
