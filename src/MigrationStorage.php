<?php

namespace Dbml;

class MigrationStorage {

    protected $path;

    public function __construct($options) {
        $this->path = $options['migrations'];
    }

    public function getList() {
        $files = $this->getFileList($this->path);
        $migrations = array();
        foreach ($files as $fullname) {
            if (strtolower(strrchr($fullname, '.')) === '.sql') {
                $migration = $this->parseSqlFileName($fullname, $files);
                if ($migration) {
                    if (isset($migrations[$migration->id])) {
                        echo "Error: {$migration['fullname']} duplicated id: {$migration->id}\n";
                        exit(1);
                    }
                    $migration->status = 'unknown';
                    $migration->created = '---------- --:--:--';
                    $migrations[$migration->id] = $migration;
                }
            }
        }
        ksort($migrations);
        return $migrations;
    }

    public function getFileList($path) {
        $path = trim($path, '/\\');
        $cut_prefix = strlen($this->path);
        $files = $this->getFileList_r($path);
        foreach ($files as $key => $fullname) {
            $files[$key] = trim(substr($fullname, $cut_prefix), '/\\');
        }
        return $files;
    }

    public function getFileList_r($path) {
        $files = array();
        $path = trim($path, '/\\');
        if (!is_dir($path)) {
            echo "Warning: migrations path [$path] not found\n";
            return array();
        }
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry[0] != '.') {
                    if (is_dir($path.'/'.$entry)) {
                        $files = array_merge($files, $this->getFileList_r($path.'/'.$entry));
                    } else {
                        $files[] = $path.'/'.$entry;    
                    }
                }
            }
            closedir($handle);
        }
        return $files;
    }

    public function parseSqlFileName($fullname, $files) {
        $filename = basename($fullname);
        if (!preg_match('/^(\d{4}-\d{2}-\d{2}-\d{3})(-.+)?\.sql$/i', $filename, $matches)) {
            if (preg_match('/sql$/i', $filename)) {
                echo "Error: {$fullname} does not match pattern yyyy-mm-dd-NNN-comment.sql\n";
                exit(1);
            }
            return false;
        }

        $id = $matches[1];
        $migration = new Migration();
        $migration->fullname = $fullname;
        $migration->id = $id;

        $base = preg_replace('/(\d{4}-\d{2}-\d{2}-\d{3})(-.+)?\.sql$/i', '\1', $fullname);
        $migration->before = $this->find($base.'-before', $files);
        $migration->after = $this->find($base.'-after', $files);

        return $migration;
    }

    public function find($begins, $files) {
        foreach ($files as $filename) {
            if (strpos($filename, $begins) === 0) {
                return $filename;
            }
        }
        return null;
    }

}
