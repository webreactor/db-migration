<?php

namespace Reactor\Database\Exceptions;

class DatabaseException extends \Exception {

    protected $context;

    public function __construct($message, $context = null) {
        parent::__construct($message);
        $this->context = $context;
    }

    public function getContext() {
        return $this->context;
    }

}
