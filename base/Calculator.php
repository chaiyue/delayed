<?php
/**
 * 算法模块
 *
 * @author CHAIYUE
 * @version 2017-07-24
 */
namespace CyDelayed\base;
class Calculator
{
    //计算Slot,cycleNum
    public static function getSlot($probe, $time, $slotNum)
    {
        $cycleNum = intval($time / $slotNum);
        if (($time % $slotNum) + $probe > $slotNum) {
            $slot = ($time % $slotNum) - $slotNum + $probe;
        } else {
            $slot = ($time % $slotNum) + $probe;
        }
        return compact('cycleNum', 'slot');
    }
}