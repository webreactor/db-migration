<?php

namespace Reactor\DbMigration;

class Migration {

    public $id;
    public $status;
    public $created;
    public $fullname;
    public $before;
    public $after;

    public static function createFromArray($data) {
        $migration = new self();
        foreach ($data as $key => $value) {
            $migration->$key = $value;
        }
        return $migration;
    }

}
