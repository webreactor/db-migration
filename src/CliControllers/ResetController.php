<?php

namespace Dbml\CliControllers;

class ResetController extends BaseController {

    public function handle() {
        $this->initTracker();
        $mirations = $this->app->getAllMigrations();
        $tracker = $this->app->getTracker();
        $cnt = 0;
        $reset_id = $this->app->parameters['reset'];
        foreach ($mirations as $migration) {
            if ($migration->id == $reset_id) {
                $this->printMigration($migration);
                $tracker->delete($migration);
                $cnt++;
            }
        }
        if ($cnt == 0) {
            throw new \Exception("migration not found '$reset_id'", 1);
        }
    }
}
