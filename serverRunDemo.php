<?php
include 'vendor/autoload.php';
$delayedObj = new delayed();
$delayedObj->setJobEvent(function ($data) {
    var_dump($data);
})->run(['swoole' => ['time' => 1000],
    'redis' => ['hash_name_prefix' => 'delayed:',
        'host' => '101.200.196.131',
        'port' => 7009]]);