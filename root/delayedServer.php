<?php
/**
 * 延时队列服务启动
 *
 * @author CHAIYUE
 * @version 2017-10-26
 */
namespace CyDelayed\root;
class delayedServer
{
    protected $slotSetServer;
    protected $timerServer;
    protected $eventServer;

    public function __construct()
    {
        include_once __DIR__ . '/../base/helper.php';
        var_dump(dyPath());
    }
}
new delayedServer();



