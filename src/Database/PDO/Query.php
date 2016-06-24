<?php

namespace Reactor\Database\PDO;

use Reactor\Database\Interfaces\QueryInterface;
use Reactor\Database\Exceptions as Exceptions;

class Query implements QueryInterface {
    protected
        $stats = array(),
        $statement,
        $line = null,
        $iterator_key = 0;

    public function __construct($statement) {
        $this->statement = $statement;
    }

    public function exec($parameters = array()) {
        $this->statement->closeCursor();
        $parameters = (array)$parameters;
        $this->stats['parameters'] = $parameters;
        $execution_time = microtime(true);
        try {
            $this->statement->execute($parameters);    
        } catch (\PDOException $exception) {
            throw new Exceptions\DatabaseException($exception->getMessage(), $this);
        }
        $this->stats['execution_time'] = microtime(true) - $execution_time;
        return $this;
    }

    public function __destruct() {
        $this->statement->closeCursor();
    }

    public function line($row = '*') {
        $line = $this->statement->fetch(\PDO::FETCH_ASSOC);
        if ($line) {
            if ($row == '*') {
                return $line;
            }
            return $line[$row];
        }
        return null;
    }

    public function free() {
        $this->statement->closeCursor();
    }

    public function matr($key = null, $row = '*') {
        $data = array();
        if ($key === null) {
            if ($row == '*') {
                $data = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                while ($line = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[] = $line[$row];
                }
            }
        } else {
            if ($row == '*') {
                while ($line = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[$line[$key]] = $line;
                }
            } else {
                while ($line = $this->statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[$line[$key]] = $line[$row];
                }
            }
        }

        return $data;
    }

    public function count() {
        return $this->statement->rowCount();
    }

    public function getStats() {
        $this->stats['query'] = $this->statement->queryString;
        return $this->stats;
    }

    // Hackish method if advanced PDO features are requred
    public function getStatement() {
        return $this->statement;
    }

    public function current() {
        if (!$this->line) {
            $this->next();
        }
        return $this->line;
    }

    public function key() {
        return $this->iterator_key;
    }

    public function next() {
        $this->line = $this->line();
        $this->iterator_key++;
        if (!$this->line) {
            $this->iterator_key = false;
        }
    }

    public function rewind() {
        return $this->iterator_key = 0;
    }
    
    public function valid() {
        return $this->iterator_key !== false;
    }

}
