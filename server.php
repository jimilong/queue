<?php
/**
 * Created by PhpStorm.
 * User: longmin
 * Date: 16/7/26
 * Time: 下午10:20
 */

/*$serv = new Swoole\Server("127.0.0.1", 9501);//设置IP地址及端口

$serv->set([
    'worker_num' => 2,//设置启动的worker进程数量
]);*/



/*$process = new Swoole\Process('callback_function', true);//1.执行一个回调函数 2.重定向进程的标准输入和输出，true 表示为不输出，将数据写入到管道
$process->start();
function callback_function(){
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    function f($redis, $clhan, $msg)
    {
        $val = $msg . '  ';
        file_put_contents('1.txt',$val,FILE_APPEND);
    }
    $redis->subscribe(array('note_list'), 'f');//接收订阅信息，回调一个名为”f“的自定义函数
}*/

//创建Server对象，监听 127.0.0.1:9501端口
/*$serv = new Swoole\Server("127.0.0.1", 9501);

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//监听数据发送事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();*/

/*$process = new Swoole\Process('callback_function', true);//1.执行一个回调函数 2.重定向进程的标准输入和输出，true 表示为不输出，将数据写入到管道
$process->start();
function callback_function($worker){
    swoole_event_add($worker->pipe, function($pipe) {
        $recv = $worker->read();
        echo "From Master: $recv\n";

    });
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    function f($redis, $clhan, $msg)
    {
        $val = $msg . '  ';
        file_put_contents('1.txt',$val,FILE_APPEND);
    }
    $redis->subscribe(array('note_list'), 'f');//接收订阅信息，回调一个名为”f“的自定义函数
}*/

/*Swoole\Process::wait(false);

//监听数据发送事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});

$serv->tick(1000, function() use ($process) {
    //$process->write("hello world son");
    echo 111111;
});


$serv->start();*/


/*
Swoole已经内置了心跳检测功能，能自动close掉长时间没有数据来往的连接。
而开启心跳检测功能，只需要设置heartbeat_check_interval和heartbeat_idle_time即可。如下：
$this->serv->set(
    array(
        'heartbeat_check_interval' => 60,
        'heartbeat_idle_time' => 600,
    )
);
其中heartbeat_idle_time的默认值是heartbeat_check_interval的两倍。
在设置这两个选项后，swoole会在内部启动一个线程
每隔heartbeat_check_interval秒后遍历一次全部连接，检查最近一次发送数据的时间和当前时间的差
如果这个差值大于heartbeat_idle_time，则会强制关闭这个连接，并通过回调onClose通知Server进程。
小技巧：
结合之前的Timer功能，如果我们想维持连接，就设置一个略小于如果这个差值大于heartbeat_idle_time的定时器，在定时器内向所有连接发送一个心跳包。
如果收到心跳回应，则判断连接正常，如果没有收到，则关闭这个连接或者再次尝试发送。
*/

class server
{
    public $serv;

    /**
     * [__construct description]
     * 构造方法中,初始化 $serv 服务
     */
    public function __construct() {
        $this->serv = new Swoole\Server('127.0.0.1', 9801);
        //初始化swoole服务
        $this->serv->set(array(
            'worker_num'  => 2,
            'daemonize'   => 0, //是否作为守护进程,此配置一般配合log_file使用
            'max_request' => 1000,
            'dispatch_mode' => 2,
            'debug_mode' => 1,
            'log_file'    => './swoole.log',
            'heartbeat_check_interval' => 5,
            'heartbeat_idle_time' => 8, //默认是heartbeat_check_interval的2倍,超过此设置客户端没有回应则强制断开链接
        ));

        //设置监听
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on("Receive", array($this, 'onReceive'));
        $this->serv->on("Close", array($this, 'onClose'));

        //开启
        $this->serv->start();
    }

    public function onStart($serv) {
        echo SWOOLE_VERSION . " onStart\n";
    }

    public function onConnect($serv, $fd) {
        echo $fd."Client Connect.\n";
    }

    public function onReceive($serv, $fd, $from_id, $data) {
        echo "Get Message From Client {$fd}:{$data}\n";
        // send a task to task worker.
        /*$param = array(
            'fd' => $fd
        );

        $serv->send($fd, 'Swoole: '.$data);*/
    }

    public function onClose($serv, $fd) {
        echo $fd."Client Close.\n";
    }

}

$server = new server();

