<?php

namespace Dbml\CliControllers;

use Symfony\Component\Yaml\Dumper as YamlDumper;

class InitController
    extends BaseController
{
    public function handle()
    {
        $fullname = getcwd() . '/db-migration.yml';

        if (file_exists($fullname)) {
            echo "Config YML file already exists\n";
            return;
        }

        $parameters = array(
            'user'            => '$MYSQL_USERNAME',
            'password'        => '$MYSQL_PASSWORD',
            'database'        => 'test',
            'migrations'      => 'db-migrations',
            'create-database' => true,
        );

        $dumper = new YamlDumper();

        $yaml = $dumper->dump($parameters, 10, 0);

        if (file_put_contents($fullname, $yaml)) {
            echo "Config db-migration.yml created\n";
        }
    }
}
