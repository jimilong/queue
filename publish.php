<?php
/**
 * Created by PhpStorm.
 * User: longmin
 * Date: 16/7/26
 * Time: 下午10:19
 */
$redis = new Redis();//实例化类
$redis->connect('127.0.0.1', 6379);//设置IP地址及端口
$num = rand(1000, 9999);
$val = $redis->publish('note_list', $num);//将数据插入到一个名为 “note_list“的队列中
if ($val) {  //返回给客户端执行结果
    echo json_encode('短信发送成功');
} else {
    echo json_encode('短信发布失败');
}