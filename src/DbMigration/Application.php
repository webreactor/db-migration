<?php

namespace Reactor\DbMigration;

class Application {

    protected $cache = array();
    public $parameters = array();

    public function __construct() {
        $this->parameters['app-version'] = '1.0.2';
    }

    public function setParameters($parameters) {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * @return MigrationStorage
     */
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

    /**
     * @throws \Exception
     * @return \Reactor\DbMigration\Drivers\DriverInterface
     */
    public function getTracker() {
        if (isset($this->cache['tracker'])) {
            return $this->cache['tracker'];
        }
        $driver_name = $this->parameters['driver'];
        $c_name = Utilities::strToClassName($driver_name);
        $c_name = 'Reactor\\DbMigration\\Drivers\\'.$c_name.'Driver';
        if(!class_exists($c_name)) {
            throw new \Exception("Unknown driver '$driver_name'");
        }
        $this->cache['tracker'] = new $c_name($this);
        return $this->cache['tracker'];
    }

    public function getAllMigrations($limit = null) {
        if (0 === $limit) {
            return array();
        }

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

        if ($limit) {
            $merged = array_slice($merged, -$limit, null, true);
        }

        return $merged;
    }

    protected function mergeMigrations($tracked_migrations, $migrations_data) {
        $ids = array_merge(array_keys($migrations_data), array_keys($tracked_migrations));
        $ids = array_unique($ids);
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
