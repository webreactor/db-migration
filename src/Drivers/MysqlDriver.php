<?php

namespace Dbml\Drivers;
use \Reactor\Database\PDO\Connection;
use \Dbml\Migration;
use \Dbml\Utilities;


class MysqlDriver implements DriverInterface {

    public $options;

    public function init($options) {
        $this->options = $options;
        $connection_string = $this->buildConnectionString($options);
        $this->connection = new Connection ($connection_string, $options['user'], $options['password']);
        $this->table = $options['table'];
        $this->selectDb();
        $this->tryCreateMigrationTable();
    }

    public function selectDb() {
        $options = $this->options;
        if (!isset($options['database'])) {
            throw new \Exception("missing mandatory parameter 'database'", 1);
        }
        if (!empty($options['create-database'])) {
            $create_statement = 'CREATE DATABASE IF NOT EXISTS `'.$options['database'].'`';
            $this->connection->sql($create_statement, array());
        }
        $this->connection->sql('USE `'.$options['database'].'`', array());
    }

    public function delete($migration) {
        $delete = 'DELETE FROM `'.$this->table.'` WHERE id=:id';
        $this->connection->sql($delete, array('id' => $migration->id));
        $migration->status = "deleted";
        return $this->connection->lastId();
    }

    public function register($migration) {
        $insert = 'INSERT INTO `'.$this->table.'` VALUES (:id, now(), :info, "registered")';
        $count = $this->connection->sql($insert, array('id' => $migration->id, 'info'=> json_encode($migration)))->count();
        $migration->status = "registered";
        return $count;
    }

    public function getList() {
        $raw = $this->connection->sql('SELECT * FROM `'.$this->table.'` order by `created`')->exec()->matr('id');
        $rez = array();
        foreach ($raw as $id => $line) {
            $rez[$id] = $this->buildMigrationObj($line);
        }
        return $rez;
    }

    protected function buildMigrationObj($line) {
        $migration = json_decode($line['info'], true);
        $migration['created'] = $line['created'];
        $migration['status'] = $line['status'];
        return Migration::createFromArray($migration);
    }

    public function setStatus($migration, $status) {
        $update = 'UPDATE `'.$this->table.'` SET `status` = :status WHERE id = :id';
        $this->connection->sql($update, array('id' => $migration->id, 'status' => $status ));
        $migration->status = $status;
    }

    protected function tryCreateMigrationTable() {
        $create_statement = 'CREATE TABLE IF NOT EXISTS `'.$this->table.'` (
            `id` CHAR(14) NOT NULL,
            `created` DATETIME NOT NULL,
            `info` TEXT NOT NULL,
            `status` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`)
        )';
        $this->connection->sql($create_statement, array());
    }

    protected function buildConnectionString($options) {
        $str = $options['driver'].':';
        $available = array(
            'host'          => false,
            'port'          => false,
            'unix_socket'   => false,
            'charset'       => false,
        );
        $ready_options = array();
        foreach ($available as $key => $mandatory) {
            if ($mandatory && !isset($options[$key])) {
                throw new \Exception("missing mandatory parameter '$key'", 1);
            }
        }
        foreach ($options as $key => $value) {
            if (isset($available[$key]) && $value != '' ) {
                $ready_options[] = "$key=$value";
            }
        }
        return $str.implode(';', $ready_options);
    }

    public function getDefaults() {
        return array(
            array('host', 'h', 'localhost'),
            array('port', '', '3306'),
            array('user', 'u', null),
            array('password', 'p', null),
            array('unix_socket', '', null),
            array('database', 'db', ''),
            array('create-database', 'c', true),
            array('extra', '', ''),
            array('table', '', 'db_migrations'),
            array('migration-file-extention', '', 'sql'),
        );
    }

    public function load($migration) {
        Utilities::exec($this->buildCmd($this->options, $migration));
    }

    protected function buildCmd($options, $migration) {
        $full_name = $this->options['migrations'].$migration->fullname;
        $rez = 'mysql -B '.Utilities::buildCmdArgs(
            $options,
            array(
            'host'          => '--host=',
            'user'          => '--user=',
            'port'          => '--port=',
            'password'      => '--password=',
            'unix_socket'   => '--socket=',
            'database'      => '--database=',
        ));
        if (isset($options['extra'])) {
            $rez .= ' '.$options['extra'];
        }
        return $rez.' < '.escapeshellarg($full_name);
    }

}

