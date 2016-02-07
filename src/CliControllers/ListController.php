<?php

namespace Dbml\CliControllers;

class ListController extends BaseController {

    public function handle() {
        $this->initTracker();
        $mirations = $this->app->getAllMigrations();

        foreach ($mirations as $migration) {
            $this->printMigration($migration);
        }
    }

}
