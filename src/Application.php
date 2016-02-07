<?php

namespace Dbml;

use Dbml\Utilities;

class Application {

    protected $cache = array();
    public $parameters = array();

    public function setParameters($parameters) {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    public function getMigrations() {
        if (isset($this->cache['migrations'])) {
            return $this->cache['migrations'];
        }
        $this->cache['migrations'] = new MigrationStorage($this->parameters);
        return $this->cache['migrations'];
    }

    public function getTrackerDefaults() {
        return $this->getTracker()->getDefaults();
    }

    public function getTracker() {
        if (isset($this->cache['tracker'])) {
            return $this->cache['tracker'];
        }
        $driver_name = $this->parameters['driver'];
        $c_name = Utilities::strToClassName($driver_name);
        $c_name = 'Dbml\\Drivers\\'.$c_name.'Driver';
        if(!class_exists($c_name)) {
            throw new \Exception("Unknown driver '$driver_name'");
        }
        $this->cache['tracker'] = new $c_name($this->app);
        return $this->cache['tracker'];
    }

    public function getAllMigrations() {
        $tracker = $this->getTracker();
        $tracked_migrations = $tracker->getList();

        $migrations = $this->getMigrations();
        $migrations_data = $migrations->getList();

        $merged = $this->mergeMigrations($tracked_migrations, $migrations_data);

        $last = end($tracked_migrations);

        $new_flag = 'stale';
        if (count($tracked_migrations) == 0) {
            $new_flag = 'new';
        }
        foreach ($merged as $id => $migration) {
            if ($last && $id === $last->id) {
                $new_flag = 'new';
            }
            if ($migration->status === 'unknown') {
                $merged[$id]->status = $new_flag;
            }
        }
        return $merged;
    }

    protected function mergeMigrations($tracked_migrations, $migrations_data) {
        $ids = array_merge(array_keys($migrations_data), array_keys($tracked_migrations));
        $ids= array_unique($ids);
        sort($ids);

        $merged = array();
        foreach($ids as $id) {
            if (isset($tracked_migrations[$id])) {
                $migration = $tracked_migrations[$id];
            } else {
                $migration = $migrations_data[$id];
            }
            $merged[$migration->id] = $migration;
        }
        return $merged;
    }

}
