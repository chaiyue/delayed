<?php
namespace CyDelayed\slotSet;

use CyDelayed\base\Calculator;
use CyDelayed\base\SlotSet;

class PhpSlotSet implements SlotSet
{
    private $slotSet = [];
    private $probe = 0;
    private $circle = 0;
    private $slotNum = 3600;

    public function probeInc($num = 1)
    {
        $new_num = $this->probe + $num;
        if ($this->slotNum - $new_num < 0) {
            $new_num = abs($this->slotNum - $new_num);
            $this->circle++;
        } elseif ($this->slotNum - $new_num == 0) {
            $new_num = 0;
            $this->circle++;
        }
        $this->probe = $new_num;
        return $this;
    }

    public function getProbe()
    {
        return $this->probe;
    }

    public function getCircle()
    {
        return $this->circle;
    }

    public function slotSetAdd($time, $data)
    {
        $slotSetInfo = Calculator::getSlot($this->probe, $time, $this->slotNum);
        $slotSet = &$this->slotSet;
        if (isset($slotSet[$slotSetInfo['slot']][$slotSetInfo['cycleNum']])) {
            $slotSet[$slotSetInfo['slot']][$slotSetInfo['cycleNum']] = array_merge($slotSet[$slotSetInfo['slot']][$slotSetInfo['cycleNum']], [$data]);
        } else {
            $slotSet[$slotSetInfo['slot']][$slotSetInfo['cycleNum']] = [$data];
        }
    }

    public function slotSetCurrent()
    {
        $slotSetCurrent = isset($this->slotSet[$this->probe]) ? $this->slotSet[$this->probe] : [];
        if (!empty($slotSetCurrent) && isset($slotSetCurrent[$this->circle])) {
            unset($this->slotSet[$this->probe][$this->circle]);
            return $slotSetCurrent[$this->circle];
        }
        return [];
    }

    public function flushAll()
    {
        $this->slotSet = [];
    }
}