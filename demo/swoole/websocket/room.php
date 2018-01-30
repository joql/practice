<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/30
 * Time: 10:38
 */

$ws = new swoole_websocket_server('0.0.0.0',9502);
$ws->user_c = [];

$ws->on('open',function ($ws,$requset){
    $ws->user_c[]=$requset->id;
});

$ws->on('message', function ($ws,$frame){
    $msg = 'from'.$frame->fd.":{$frame->data}\n";
    foreach ($ws->user_c as $v){
        $ws->push($v,$msg);
    }
});

$ws->on('close',function ($ws,$fd){
    unset($ws->user_c['$fd-1']);
});

$ws->start();
