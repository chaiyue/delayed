<?php
/**
 * 启动
 *
 * @author CHAIYUE
 * @version 2017-10-26
 */
namespace CyDelayed\root;

use CyDelayed\timer\SwooleTimer;
use CyDelayed\slotSet\PhpSlotSet;
use CyDelayed\slotSet\RedisSlotSet;
use CyDelayed\base\JobEvent;
use CyDelayed\base\RedisServer;

class delayed
{
    private $slot_num = 3600;
    private $time_gran = 1000;
    private $send_ip = null;
    private $send_port = null;

    public function run($params = [])
    {
        $configs = $this->setConf($params);
        if (empty($configs['redisConf'])) {
            $SlotSet = new RedisSlotSet();
            $SlotSet->setRedis(new RedisServer($configs['redisConf']));
        } else {
            $SlotSet = new PhpSlotSet();
        }
        $SwooleTimerObj = new SwooleTimer();
        $SwooleTimerObj
            ->setConfig($configs['swooleConf'])
            ->setSlot($SlotSet)
            ->begin();
    }

    public function setJobEvent($concrete)
    {
        $concrete = is_string($concrete) ? ltrim($concrete, '\\') : $concrete;
        if (is_array($concrete)) {
            list($abstract, $func) = $concrete;
        }
        if (!$concrete instanceof Closure) {
            $concrete = function ($data) use ($abstract, $func) {
                $abstractObj = new $abstract();
                $abstractObj->$func($data);
            };
        }
        JobEvent::set($concrete);
        return $this;
    }

    private function setConf($params)
    {
        //开启swoole配置文件
        $swooleParams = [];
        if (isset($params['swoole'])) {
            $swooleParams = $params['swoole'];
        }
        $swooleConf = function () use ($swooleParams) {
            $this->worker_num = 1;
            $this->daemonize = false;
            $this->max_request = 10000;
            $this->time = 1000;
            foreach ($swooleParams as $key => $val) {
                $this->$key = $val;
            }
        };
        //开启Redis配置文件
        $redisConf = [];
        if (isset($params['redis'])) {
            $redisConf = array_merge(['hash_name_prefix' => 'delayed:',
                'host' => '127.0.0.1',
                'port' => 3306], $params['redis']);
        }
        return compact('redisConf', 'swooleConf');
    }

    public function setSendConfig($config)
    {
        $this->send_ip = $config['ip'];
        $this->send_port = $config['port'];
        return $this;
    }

    public function sendSortSet($time, $data)
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect($this->send_ip, $this->send_port, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $client->send(serialize(['time' => $time, 'data' => $data]));
        echo $client->recv();
        $client->close();
    }
}