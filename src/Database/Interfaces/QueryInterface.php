<?php

namespace Reactor\Database\Interfaces;

interface QueryInterface extends \Iterator {

    public function exec($parameters = array());
    public function line($row = '*');
    public function free();
    public function matr($key = null, $row = '*');
    public function count();
    public function getStats();
    public function current();
    public function key();
    public function next();
    public function rewind();
    public function valid();
}
