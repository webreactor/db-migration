<?php

namespace Dbml;

use Symfony\Component\Yaml\Yaml;

class Utilities
{
    public static function exec($cmd)
    {
        $rez = 0;
        echo "$cmd\n";
        passthru($cmd, $rez);
        if ($rez != 0) {
            throw new \Exception("Error: executing '$cmd'", 1);
        }
    }

    public static function buildCmdArgs($options, $available)
    {
        $rez = array();
        foreach ($options as $key => $value) {
            if (isset($available[$key]) && $value !== null) {
                $rez[] = $available[$key] . escapeshellarg($value);
            }
        }

        return implode(' ', $rez);
    }

    public static function loadConfig($file)
    {
        $file_options = self::parseYAML(file_get_contents($file), true);

        return Utilities::resolveEnv($file_options);
    }

    public static function resolveEnv($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::resolveEnv($value);
            }
        } else {
            if ($data[0] == '$' && strlen($data) > 2) {
                $data = substr($data, 1);
                if ($data[1] == '$') {
                    return $data;
                }

                return getenv($data);
            }
        }

        return $data;
    }

    public static function strToClassName($str)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    }

    public static function parseYAML($input, $exceptionOnInvalidType = false, $objectSupport = false, $objectForMap = false)
    {
        return Yaml::parse($input, $exceptionOnInvalidType, $objectSupport, $objectForMap);
    }

    public static function dumpYAML($array, $inline = 2, $indent = 4, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        return Yaml::dump($array, $inline, $indent, $exceptionOnInvalidType, $objectSupport);
    }
}
