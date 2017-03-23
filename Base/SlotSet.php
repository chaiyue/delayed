<?php
namespace Delayed\Base;
interface SlotSet
{
    //添加新任务
    public function slotSetAdd($time, $data);

    //获取一个当前任务
    public function slotSetCurrent();

    //指针自增
    public function probeInc($num);

    //获取指针
    public function getProbe();

    //获取圈数
    public function getCircle();

    //清空
    public function flushAll();
}