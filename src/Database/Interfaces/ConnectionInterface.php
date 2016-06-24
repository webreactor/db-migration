<?php

namespace Reactor\Database\Interfaces;

interface ConnectionInterface {

    public function sql($query);
    public function lastId($name = null);
    public function insert($table, $data);
    public function replace($table, $data);
    public function update($table, $data, $where_data = array(), $where = '');
    public function pages($query, $parameters, $page, $per_page, $total_rows = null);

}
