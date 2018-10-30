<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/10/30
 * Time: 14:19
 */



$start = time();
echo 'start '.$start."\n";
for($i=0;$i<2000;$i++){
    $ip = ipLocation('1.198.23.254');
    //echo 'ip '.$ip[1]."\n";
}
$end = time();
echo 'end '.$end."\n";
echo 'count '.$i."\n";
echo 'runTime '.($end-$start)."s\n";
echo "//*****************//\n";

//********************************

//最快
$start = time();
echo 'start '.$start."\n";
for($i=0;$i<2000;$i++){
    $ip = ip('1.198.23.254');
    //echo 'ip '.$ip[1]."\n";
}
$end = time();
echo 'end '.$end."\n";
echo 'count '.$i."\n";
echo 'runTime '.($end-$start)."s\n";
echo "//*****************//\n";
//*****************************
$start = time();
echo 'start '.$start."\n";
for($i=0;$i<2000;$i++){
    $ip = ip2Region('1.198.23.254');
    //echo 'ip '.$ip[1]."\n";
}
$end = time();
echo 'end '.$end."\n";
echo 'count '.$i."\n";
echo 'runTime '.($end-$start)."s\n";
echo "//*****************//\n";
//*************************************************
function ipLocation($ip = '127.0.0.1'){
    require_once 'IpLocation.php';
    $data = json_decode(json_encode(\itbdw\Ip\IpLocation::getLocation($ip), JSON_UNESCAPED_UNICODE),true);
    return $data['province'];
}
function ip($ip = '127.0.0.1'){
    require_once 'Ip.php';
    $data = \Zhuzhichao\IpLocationZh\Ip::find($ip);
    return $data;
}

function ip2Region($ip = '127.0.0.1'){
    require_once dirname(__FILE__) . '/Ip2Region.class.php';
    $ip2regionObj = new Ip2Region('./ip2region.db');
    return $ip2regionObj->btreeSearch('1.198.23.110');
}

