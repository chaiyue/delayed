<?php
namespace Delayed\Timer;

use Delayed\Base\JobEvent;
use swoole_server;
use swoole_timer_tick;
use Delayed\Base\SlotSet;

class SwooleTimer
{
    public $serv = null;
    private $worker_num = 2;
    private $daemonize = false;
    private $max_request = 10000;
    private $time = 1000;
    private $server_ip = '127.0.0.1';
    private $server_port = 2008;
    private $slotSet = null;

    public function begin()
    {
        $this->serv = new swoole_server($this->server_ip, $this->server_port);
        $this->serv->set([
            'reactor_num' => 8,
            'task_worker_num' => 4,
            'worker_num' => $this->worker_num,
            'max_request' => $this->max_request,
            'dispatch_mode' => 3,
            'debug_mode' => 1,
            'daemonize' => $this->daemonize
        ]);
        $this->serv->on('Start', function ($serv) {
            echo "Start\n";
        });
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    public function setConfig($config)
    {
        $setup = $config->bindTo($this, __CLASS__);
        $setup();
        return $this;
    }

    public function setSlot(SlotSet $slotSet)
    {
        $this->slotSet = $slotSet;
        return $this;
    }

    public function onConnect($serv, $fd, $from_id)
    {
        $serv->send($fd, "Hello {$fd}!");
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        $data = unserialize($data);
        $this->slotSet->slotSetAdd($data['time'], $data['data']);
        $serv->send($fd, "延时队列通知成功\n");
    }

    public function onWorkerStart($serv, $worker_id)
    {
        if ($worker_id == 0) {
            //设置定时器
            swoole_timer_tick($this->time, function ($timer_id) use ($worker_id) {
                echo '当前指针:' . $this->slotSet->getProbe() . "\n";
                $data = $this->slotSet->slotSetCurrent();
                $this->slotSet->probeInc($this->time / 1000);
                if ($data) {
                    $this->serv->task($data);
                }
            });
            echo 'work进程启动 work_id:' . $worker_id . '时间:' . date('Y-m-d H:i:s') . "\n";
        }
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        $JobEventObj = new JobEvent();
        $JobEventObj->handler($data);
    }

    public function onFinish($serv, $task_id, $data)
    {
    }
}