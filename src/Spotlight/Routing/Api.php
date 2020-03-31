<?php

namespace Spotlight\Routing;

class Api
{
    public static function get($uri, $action)
    {
        self::registerRoute('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        self::registerRoute('POST', $uri, $action);
    }

    public static function put($uri, $action)
    {
        self::registerRoute('PUT', $uri, $action);
    }

    public static function patch($uri, $action)
    {
        self::registerRoute('PATCH', $uri, $action);
    }

    public static function delete($uri, $action)
    {
        self::registerRoute('DELETE', $uri, $action);
    }

    public static function registerRoute($method, $uri, $action)
    {
        add_action('rest_api_init', function () use ($method, $uri, $action) {
            register_rest_route( PLUGIN_NAME , $uri, [
                'methods' => $method,
                'callback' => function() use ($action) {
                    $function = explode('@', $action);
                    $controller = "\App\Http\Controllers\\" . $function[0];
                    echo json_encode(call_user_func([ new $controller, $function[1] ]));
                },
            ]);
        });
    }
}