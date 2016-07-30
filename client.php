<?php
/**
 * Created by PhpStorm.
 * User: longmin
 * Date: 16/7/29
 * Time: 上午9:02
 */
$client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function($cli) {
    $cli->send("GET / HTTP/1.1\r\n\r\n");
});
$client->on("receive", function($cli, $data){
    echo "Receive: $data";
    $cli->send(str_repeat('A', 100)."\n");
    sleep(1);
});
$client->on("error", function($cli){
    echo "error\n";
});
$client->on("close", function($cli){
    echo "Connection close\n";
});
$client->connect('127.0.0.1', 9801);

Swoole\Timer::tick(1000, function() use ($client) {
    $client->send("hello world");
});