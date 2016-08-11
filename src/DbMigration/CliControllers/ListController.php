<?php

namespace Reactor\DbMigration\CliControllers;

class ListController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->initTracker();

        $limit = null;
        $words = $this->app->parameters['_words_'];
        if (isset($words[2])) {
            $limit = $words[2];
        }

        $migrations = $this->app->getTrackedMigrations($limit);

        foreach ($migrations as $migration) {
            $this->printMigration($migration);
        }
    }

}
