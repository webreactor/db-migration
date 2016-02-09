<?php

namespace Dbml\CliControllers;

class ListController extends BaseController {

    public function handle() {
        $this->initTracker();

        if (false === $this->app->parameters['list']) {
            $limit = null;
        } else {
            $limit = intval($this->app->parameters['list']);
        }

        $migrations = $this->app->getMergedMigrations($limit);

        foreach ($migrations as $migration) {
            $this->printMigration($migration);
        }
    }

}
