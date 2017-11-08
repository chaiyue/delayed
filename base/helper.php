<?php
if (!function_exists('dyConfig')) {
    function dyConfig()
    {
        static $config;
        if (empty($config)) {
            $config = include_once dyPath() . 'config.php';
        }
        return $config;
    }
}
if (!function_exists('dyPath')) {
    function dyPath()
    {
        return dirname(__DIR__);
    }
}
if (!function_exists('dyApp')) {
    function dyApp()
    {
        return [];
    }
}