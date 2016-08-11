<?php

namespace Reactor\DbMigration\CliControllers;

class ResetController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();
        $mirations = $this->app->getTrackedMigrations();
        $tracker = $this->app->getTracker();
        $cnt = 0;

        $words = $this->app->parameters['_words_'];
        if (!isset($words[2])) {
            throw new \Exception("Must specify migration id", 1);
        }
        $reset_id = $words[2];

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
