<?php

namespace Dbml\CliControllers;

use Dbml\Utilities;

class ConfigController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->loadMigrationParameters();
        $this->printParameters();
    }

}
