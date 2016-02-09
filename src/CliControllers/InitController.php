<?php

namespace Dbml\CliControllers;

class InitController
    extends BaseController
{
    public function handle()
    {
        $filename = 'db-migration.yml';

        $source = __DIR__ . '/../../' . $filename;
        $dest   = getcwd() . '/' . $filename;

        if (file_exists($dest)) {
            throw new \Exception('Config YML file already exists');
        }

        if (copy($source, $dest)) {
            echo "Config db-migration.yml created\n";
        } else {
            throw new \Exception('Can\'t create config file');
        }
    }
}
