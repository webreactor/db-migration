<?php

namespace Dbml\CliControllers;

class ResetController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();
        $mirations = $this->app->getAllMigrations();
        $tracker = $this->app->getTracker();
        $cnt = 0;

        $words = $this->request->get('_words_');
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
