<?php

namespace Dbml\CliControllers;

class CreateController
    extends BaseController
{
    public function handle()
    {
        $this->initTracker();

        $migrations =
            $this->app->getMigrations()
                ->getList();

        if (count($migrations)) {
            $last_migration = end($migrations);

            $last_migration_id_arr = explode('-', $last_migration->id);

            $id = str_pad($last_migration_id_arr[3] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $id = '001';
        }

        $new_migration_name = sprintf(
            '%s-%s%s.%s',
            date('Y-m-d'),
            $id,
            false === $this->app->parameters['create'] ? '' : '-' . $this->app->parameters['create'],
            $this->app->parameters['migration-file-extention']
        );

        $fullname = $this->app->parameters['migrations'] . $new_migration_name;

        if (false !== file_put_contents($fullname, '')) {
            echo "Created new empty migration file $new_migration_name\n";
        } else {
            echo "Error\n";
        }
    }
}
