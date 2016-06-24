<?php

namespace Reactor\DbMigration\CliControllers;

class CreateController extends BaseController {
    public function handle($request) {
        parent::handle($request);
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

        $words = $this->app->parameters['_words_'];
        $name = false;
        if (isset($words[2])) {
            $name = $words[2];
        }

        if ($name !== false) {
            $name = preg_replace('/[^\w]/', '-', $name);
            $migration_filename .= '-' . $name;
        }

        $migration_filename .= '.' . $this->app->parameters['migration-file-extention'];

        $migration_fullname = 
            $this->app->parameters['pwd'].
            $this->app->parameters['migrations'][0].
            $migration_filename;

        if (file_put_contents($migration_fullname, '') !== false) {
            echo "Created empty migration file $migration_filename\n";
        } else {
            throw new \Exception('Can\'t create new migration file');
        }
    }

    private function getLastMigrationParsedId() {
        $this->loadMigrationParameters();

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
