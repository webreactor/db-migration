<?php

namespace Dbml\CliControllers;

class CreateController extends BaseController {
    public function handle() {
        $date   = date('Y-m-d');
        $number = 1;

        $last_migration = $this->getLastMigrationParsedId();

        if ($last_migration) {
            if ($date == $last_migration['date']) {
                $number = $last_migration['number'] + 1;
            }
        }

        $migration_filename = sprintf(
            '%s-%s',
            $date,
            str_pad($number, 3, '0', STR_PAD_LEFT)
        );

        if ($this->app->parameters['create'] !== false) {
            $migration_filename .= '-' . $this->app->parameters['create'];
        }

        $migration_filename .= '.' . $this->app->parameters['migration-file-extention'];

        $migration_fullname = $this->app->parameters['migrations'] . $migration_filename;

        if (file_put_contents($migration_fullname, '') !== false) {
            echo "Created new empty migration file $migration_filename\n";
        } else {
            throw new \Exception('Can\'t create new migration file');
        }
    }

    private function getLastMigrationParsedId() {
        $this->initTracker();

        $migrations =
            $this->app->getMigrations()
                ->getList();

        $last_migration = end($migrations);

        if (!$last_migration) {
            return false;
        }

        $tmp = explode('-', $last_migration->id);

        return array(
            'date'   => mb_substr($last_migration->id, 0, 10),
            'number' => $tmp[3],
        );
    }
}
