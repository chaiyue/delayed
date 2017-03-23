<?php
namespace Delayed\Base;

class JobEvent
{
    private static $concrete = null;

    public static function set($concrete)
    {
        self::$concrete = $concrete;
    }

    public function handler($data)
    {
        $concrete = self::$concrete->bindTo($this, __CLASS__);
        $concrete($data);
    }
}