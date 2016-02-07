<?php

namespace Reactor\Database\PDO;

use Reactor\Database\Interfaces\ConnectionInterface;
use Reactor\Database\Exceptions as Exceptions;

class Connection implements ConnectionInterface {
    protected 
        $connection = null,
        $connection_string,
        $user,
        $pass;

    public function __construct($connection_string, $user = null, $pass = null) {
        $this->connection_string = $connection_string;
        $this->user = $user;
        $this->pass = $pass;
    }

    protected function getConnection() {
        if ($this->connection === null) {
            try {
                $this->connection = new \PDO($this->connection_string, $this->user, $this->pass);
                $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $exception) {
                throw new Exceptions\DatabaseException($exception->getMessage(), $this);
            }
        }
        return $this->connection;
    }

    public function transaction($func, $param = array()) {
        if (!is_callable($func) || !is_array($param)) {
            return false;
        }
        try {
            $this->beginTransaction();
            call_user_func_array($func, $param);
            return $this->commit();
        } catch (\Exception $exception) {
            $this->rollBack();
            throw new Exceptions\DatabaseException('Transaction failed - ' . $exception->getMessage(), $this);
        }
    }

    public function beginTransaction() {
        try {
            return $this->getConnection()->beginTransaction();
        } catch (\Exception $exception) {
            throw new Exceptions\DatabaseException($exception->getMessage(), $this);
        }
    }

    public function commit() {
        try {
            return $this->getConnection()->commit();
        } catch (\Exception $exception) {
            throw new Exceptions\DatabaseException($exception->getMessage(), $this);
        }
    }

    public function rollBack() {
        try {
            return $this->getConnection()->rollBack();
        } catch (\Exception $exception) {
            throw new Exceptions\DatabaseException($exception->getMessage(), $this);
        }
    }

    public function sql($query, $arguments = null) {
        $statement = $this->getConnection()->prepare($query);
        if (!$statement) {
            throw new Exceptions\DatabaseException($this->getConnection()->errorInfo()[2], $this);
        }
        $query = new Query($statement);
        if ($arguments === null) {
            return $query;
        }
        return $query->exec($arguments);
    }

    public function lastId($name = null) {
        return $this->getConnection()->lastInsertId($name);
    }

    protected function wrapWhere($where) {
        if (trim($where) == '') {
            return ' ';
        }
        return ' where '.$where;
    }

    public function select($table, $where_data = array(), $where = '') {
        if ($where === '') {
            $where = $this->buildPairs(array_keys($where_data), 'and');
        }
        return $this->sql('select * from `' . $table . '`'
            . $this->wrapWhere($where), $where_data);
    }

    public function insert($table, $data) {
        $keys = array_keys($data);
        $this->sql('insert into `'.$table.'`
            (`' . implode('`, `', $keys) . '`)
            values (:' . implode(', :', $keys) . ')', $data);
        return $this->lastId();
    }

    public function replace($table, $data) {   
        $keys = array_keys($data);
        $this->sql('replace into `'.$table.'`
            (`' . implode('`, `', $keys) . '`)
            values (:' . implode(', :', $keys) . ')', $data);
        return $this->lastId();
    }

    public function buildPairs($keys, $delimeter = ',') {
        $pairs = array();
        foreach ($keys as $k) {
            $pairs[] = '`' . $k . '`= :' . $k;    
        }
        return implode(' ' . $delimeter . ' ', $pairs);
    }

    public function update($table, $data, $where_data = array(), $where = '') {
        if ($where === '') {
            $where = $this->buildPairs(array_keys($where_data), 'and');
        }
        $query = $this->sql('update `' . $table . '` set '
            . $this->buildPairs(array_keys($data)) 
            . $this->wrapWhere($where), array_merge($data, $where_data));
        return $query->count();
    }

    public function delete($table, $where_data = array(), $where = '') {
        if ($where === '') {
            $where = $this->buildPairs(array_keys($where_data), 'and');
        }
        $query = $this->sql('delete from `' . $table . '` '
            . $this->wrapWhere($where), $where_data);
        return $query->count();
    }

    public function pages($query, $parameters, $page, $per_page, $total_rows = null) {
        $per_page = (int)$per_page;
        $page = (int)$page;

        if($page == 0) {
            $data = $this->sql($query, $parameters)->matr();
        } else {

            $from = ($page - 1)  * $per_page;
            $data = $this->sql($query . ' limit ' . $from . ', ' . $per_page, $parameters)->matr();
        }

        if ($total_rows === null) {
            $cnt_query = stristr('from', $query);

            $t = strripos($cnt_query, 'order by');
            if($t !== false) {
                $cnt_query = substr($cnt_query, 0, $t);
            }

            $total_rows = $this->sql('SELECT count(*) as `count` ' . $cnt_query)->line('count');
        }

        $total_pages = ceil($total_rows / $per_page);
        return array(
            'data' => $data,
            'total_rows' => $total_rows,
            'total_pages' => $total_pages,
            'page' => $page,
            'per_page' => $per_page,
        );
    }

}
