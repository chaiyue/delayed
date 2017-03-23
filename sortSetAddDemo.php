<?php
include 'vendor/autoload.php';
$delayedObj = new delayed();
$delayedObj->setSendConfig(['ip' => '127.0.0.1', 'port' => '2008'])->sendSortSet(6,['sss']);