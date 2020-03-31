<?php

namespace Spotlight\Routing;

class Web
{
    public static function get($uri, $action)
    {
        self::registerRoute($uri, $action);
    }

    public static function registerRoute($uri, $action)
    {
        add_action('init', function() use ($uri, $action) {
            $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
            if ( $url_path === ltrim($uri, '/') ) {
                $function = explode('@', $action);
                $controller = "\App\Http\Controllers\\" . $function[0];
                echo call_user_func([ new $controller, $function[1] ]);
                die();
            }
        });
    }
}
