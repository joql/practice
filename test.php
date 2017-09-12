<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:08
 */
require './init.php';
$tels = array();
for($i=20;$i<=20;){
    $url = 'http://www.zzhmw.com/ydxh.asp?offset='.$i;
    $content = curlRequest($url,'get');
    preg_match_all('/1\d{10}/',$content,$arr);
    $tels = array_merge($tels,$arr[0]);
    $i +=20;
    sleep(20);
    unset($url,$content);
}
echo count($tels).'<br />';
var_dump($tels);
