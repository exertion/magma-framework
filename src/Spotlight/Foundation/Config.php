<?php

namespace Spotlight\Foundation;

class Config
{
    protected static $dir;
    protected static $file;

    public function __construct($dir, $file) {
        self::$dir = $dir;
        self::$file = $file;
    }

    public function config($option, $default = null)
    {
        $config = $this->loadConfiguration();

        // Loop through dot notation and return config/array value.
        $option = strtok($option, '.');
        while ($option !== false) {
            $config = $config[$option];
            $option = strtok('.');
        }
        return !is_null($config) ? $config : $default;
    }

    public function loadConfiguration() {
        $directory = self::$dir . '/config/';
        $configs = array_diff(scandir($directory), ['..', '.']);
        $config = [];
        foreach ($configs as $file) {
            $key = str_replace('.php', '', $file);
            $config[$key] = include $directory . $file;
        }
        $config['magma'] = $this->magmaConfig();
        return $config;
    }

    public function magmaConfig() {
        return [
            'plugin_basename' => plugin_basename(self::$file)
        ];
    }
}
