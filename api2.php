<?php



$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
$allow_origin = array(
    'http://console.nuoyun.tv',
    'http://cc.nuoyun.tv'
);
if(in_array($origin, $allow_origin)){
    header('Access-Control-Allow-Origin:'.$origin);
}

//连接redis
//$redis = new \Redis();
//$redis->connect('r-bp110cefb31f3cf4.redis.rds.aliyuncs.com',6379);
//$redis->auth('wechat%Redis@Nuoyun==%2018&') === false && returnAjax(0,'err');

$act = $_REQUEST['act'];
$request = $_REQUEST;



$get = function()use($request, $redis){echo '12';
    $pid = $request['pid'];
    if(empty($pid)){
        returnAjax(0,'err');
    }
    $redis->exists($pid) === false ? returnAjax(0,'err') : returnAjax(1,'success', $redis->get($pid));
};

$add = function () use ($request, $redis){
    $pid = $request['pid'];
    $num = $request['num'];
    if(empty($pid) || empty($num)){
        returnAjax(0,'err');
    }
    $redis->exists($pid) === false && returnAjax(0,'err');
    $redis->set($pid, $redis->get($pid)+$num) === true ? returnAjax(1,'success') : returnAjax(0,'err');
};
$get();
function returnAjax($code, $msg = '', $data = array()){
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(array('code' => $code, 'data' => $data, 'msg' => $msg)));
}