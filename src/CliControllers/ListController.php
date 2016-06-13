<?php

namespace Dbml\CliControllers;

class ListController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();

        $limit = null;
        $words = $this->request->get('_words_');
        if (isset($words[2])) {
            $limit = $words[2];
        }

        $migrations = $this->app->getAllMigrations($limit);

        foreach ($migrations as $migration) {
            $this->printMigration($migration);
        }
    }

}
