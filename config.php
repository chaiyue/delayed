<?php
/**
 * 配置文件
 *
 * @author CHAIYUE
 * @version 2017-10-26
 */
return [
    //基础配置参数
    'base' => [
        'slotNum' => '3600',//一圈的槽数
    ],
    //redis链接配置(多个请依次后排)
    'redis' => [
        'namePrefix' => '',//执行过程产生的前缀
        [
            'host' => '127.0.0.1',//地址
            'port' => '7009',//端口
            'auth' => '',//密码
        ],
    ],
    //注册树
    'bootScript' => [
        'slotSetServer' => CyDelayed\SlotSet\RedisSlotSet::class,
        'timerServer' => CyDelayed\SlotSet\RedisSlotSet::class,
        'eventServer' => CyDelayed\Base\JobEvent::class
    ],
];