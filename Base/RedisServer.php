<?php
namespace Delayed\Base;

use Redis;

class RedisServer extends Redis
{
    public $hash_name_prefix = null;

    public function __construct($config)
    {
        $this->hash_name_prefix = $config['hash_name_prefix'];
        parent::connect($config['host'], $config['port']);
    }
}