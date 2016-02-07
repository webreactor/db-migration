<?php

namespace Dbml\Drivers;

interface DriverInterface {

    public function init($options);

    public function register($migration);

    public function getDefaults();

    public function getList();

    public function setStatus($migration, $status);

    public function load($migration);

    public function delete($migration);
}

