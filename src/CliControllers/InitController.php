<?php

namespace Dbml\CliControllers;

class InitController extends BaseController {

    public function handle($request) {
        parent::handle($request);
        $this->loadMigrationParameters();
        $filename = 'db-migration.yml';

        $source = __DIR__ . '/../../' . $filename;
        $dest   = getcwd() . '/' . $filename;

        if (!is_file($dest)) {
            if (copy($source, $dest)) {
                echo "$filename is created\n";
            } else {
                throw new \Exception("Can't create $filename. Check permissions?");
            }
        } else {
            echo "$filename already exists\n";
        }

        foreach ($this->app->parameters['migrations'] as $path) {
            echo $path.' ';
            if (!is_dir(getcwd().'/'.$path)) {
                if (mkdir($path, 0777, true)) {
                    echo "created\n";
                } else {
                    throw new \Exception("Can't create migrations folder. Check permissions?");
                }
            } else {
                echo "already exists\n";
            }
        }

    }
}
