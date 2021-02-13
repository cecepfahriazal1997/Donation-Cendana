<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = function() {
    if (in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1', '::1' ))) {
        $dotenv = Dotenv\Dotenv::createMutable(FCPATH.'../configuration/', 'default');
    } else {
        $server_name = $_SERVER['SERVER_NAME'];
        // $base_app_path = "/var/www/html/";

        $server_name = str_replace('api-', "", $server_name);
        // $config_path = $base_app_path . "configuration/";
        $config_path = "configuration/";

        if (file_exists($config_path . $server_name)) {
            $dotenv = Dotenv\Dotenv::createMutable($config_path, $server_name);
        } else {
            $dotenv = Dotenv\Dotenv::createMutable(FCPATH.'../configuration/', $_SERVER['SERVER_NAME']);
        }
    }
    $dotenv->load();
};