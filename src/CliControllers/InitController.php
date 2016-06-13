<?php

namespace Dbml\CliControllers;

class InitController extends BaseController {
    public function handle($request) {
        parent::handle($request);
        $filename = 'db-migration.yml';

        $source = __DIR__ . '/../../' . $filename;
        $dest   = getcwd() . '/' . $filename;

        if (is_file($dest)) {
            throw new \Exception('Config YML file already exists');
        }

        if (copy($source, $dest)) {
            echo "Config db-migration.yml created\n";
        } else {
            throw new \Exception('Can\'t create config file');
        }
    }
}
