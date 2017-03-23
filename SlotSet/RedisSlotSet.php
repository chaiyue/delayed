<?php
namespace Delayed\SlotSet;

use Delayed\Base\Calculator;
use Delayed\Base\SlotSet;
use Redis;

class RedisSlotSet implements SlotSet
{
    private static $redis = null;
    private static $hash_name_prefix = null;
    private static $hash_record_name = null;
    private static $hash_slot_name = null;
    public $slotNum = 3600;

    public function setRedis(Redis $redis)
    {
        self::$redis = $redis;
        self::$hash_name_prefix = $redis->hash_name_prefix;
        self::$hash_record_name = $redis->hash_name_prefix . 'record';
        self::$hash_slot_name = $redis->hash_name_prefix . 'slot';
    }

    public function probeInc($num = 1)
    {
        $new_num = $this->getProbe() + $num;
        if ($this->slotNum - $new_num < 0) {
            $new_num = abs($this->slotNum - $new_num);
            self::$redis->HINCRBY(self::$hash_record_name, 'circle', 1);
        } elseif ($this->slotNum - $new_num == 0) {
            $new_num = 0;
            self::$redis->HINCRBY(self::$hash_record_name, 'circle', 1);
        }
        self::$redis->hset(self::$hash_record_name, 'probe', $new_num);
        return $this;
    }

    public function getProbe()
    {
        $probe = self::$redis->hget(self::$hash_record_name, 'probe');
        return $probe ? $probe : 0;
    }

    public function getCircle()
    {
        $circle = self::$redis->hget(self::$hash_record_name, 'circle');
        return $circle ? $circle : 0;
    }

    public function slotSetAdd($time, $data)
    {
        $slotSetInfo = Calculator::getSlot($this->getProbe(), $time, $this->slotNum);
        $slotSet = self::$redis->Hget(self::$hash_slot_name, $slotSetInfo['slot']);
        $slotSet = $slotSet ? unserialize($slotSet) : null;
        if (isset($slotSet[$slotSetInfo['cycleNum']])) {
            $slotSets = $slotSet[$slotSetInfo['cycleNum']];
            array_push($slotSets, $data);
        } else {
            $slotSets[$slotSetInfo['cycleNum']] = [$data];
        }
        self::$redis->Hset(self::$hash_slot_name, $slotSetInfo['slot'], serialize($slotSets));
    }

    public function slotSetCurrent()
    {
        $slotSet = self::$redis->hget(self::$hash_slot_name, $this->getProbe());
        $slotSet = $slotSet ? unserialize($slotSet) : null;
        if (!empty($slotSet) && isset($slotSet[$this->getCircle()])) {
            $slotSetCurrent = $slotSet[$this->getCircle()];
            unset($slotSet[$this->getCircle()]);
            if (empty($slotSet)) {
                self::$redis->hdel(self::$hash_slot_name, $this->getProbe());
            } else {
                self::$redis->hset(self::$hash_slot_name, $this->getProbe(), serialize($slotSet));
            }
            return $slotSetCurrent;
        }
        return [];
    }

    public function flushAll()
    {
        self::$redis->del(self::$hash_slot_name);
        self::$redis->del(self::$hash_record_name);
    }
}