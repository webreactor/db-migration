<?php

namespace Dbml;

class MigrationStorage {

    protected $path;

    public function __construct($options) {
        $this->path = $options['migrations'];
        $this->file_extention = $options['migration-file-extention'];
    }

    public function getList() {
        $files = $this->getFileList($this->path);
        $migrations = array();
        foreach ($files as $fullname) {
            $migration = $this->parseMigrationFileName($fullname, $files);
            if ($migration) {
                if (isset($migrations[$migration->id])) {
                    throw new \Exception("{$migration->fullname} duplicated id: {$migration->id}", 1);
                }
                $migration->status = 'unknown';
                $migration->created = '---------- --:--:--';
                $migrations[$migration->id] = $migration;
            }
        }
        ksort($migrations);
        return $migrations;
    }

    public function getFileList($path) {
        $cut_prefix = strlen($this->path);
        $files = $this->getFileList_r($path);
        foreach ($files as $key => $fullname) {
            $files[$key] = trim(substr($fullname, $cut_prefix), '/\\');
        }
        return $files;
    }

    public function getFileList_r($path) {
        $files = array();
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


    public function parseMigrationFileName($fullname, $files) {
        $filename = basename($fullname);
        $file_extention = preg_quote('.' . $this->file_extention);
        if (!preg_match('/^(\d{4}-\d{2}-\d{2}-\d{3})([^\d].*)?'.$file_extention.'$/i', $filename, $matches)) {
            return false;
        }

        if (isset($matches[2])) {
            if (strpos($matches[2], '-before') === 0 || strpos($matches[2], '-after') === 0) {
                return false;
            }
        }

        $id = $matches[1];
        $migration = new Migration();
        $migration->fullname = $fullname;
        $migration->id = $id;

        $base = preg_replace('/(\d{4}-\d{2}-\d{2}-\d{3})[^\/]+$/i', '\1', $fullname);
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
