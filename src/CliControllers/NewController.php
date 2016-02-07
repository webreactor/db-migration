<?php

namespace Dbml\CliControllers;

class NewController extends BaseController {

    public function handle() {
        $this->initTracker();
        $mirations = $this->app->getAllMigrations();

        foreach ($mirations as $migration) {
            if ($migration->status == 'new') {
                $this->printMigration($migration);
            }
        }
    }
}
