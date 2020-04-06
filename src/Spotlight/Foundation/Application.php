<?php

namespace Spotlight\Foundation;

class Application
{
    protected static $dir;
    protected static $file;

    public function __construct($dir) {
        self::$dir = $dir;
        self::$file = $file;
        $this->setting = 'App\Providers\SettingServiceProvider';
        $this->init();
    }

    public function init() {
        require_once self::$dir . '/routes/api.php';
        require_once self::$dir . '/routes/web.php';
        $settings = new $this->setting;
        $settings->booted();
    }
}
