<?php

namespace Reactor\DbMigration\CliControllers;

use Reactor\DbMigration\Utilities;

class ConfigController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->loadMigrationParameters();
        $this->printParameters();
    }

}
