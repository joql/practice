<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/6/13
 * Time: 9:39
 */

require '../../init.php';

use Qiniu\Auth;
use GuzzleHttp\Client;
error_reporting(E_ALL);;

//重点 在zone.php文件中选择空间对应的服务器 如 华北 iovip-z1.qbox.me
$accessKey = '';
$secretKey = '';

$auth = new Auth($accessKey, $secretKey);




$EncodedURL = \Qiniu\base64_urlSafeEncode('http://fanyi.baidu.com/static/webpage/img/download/spritesheet.png');
$EncodedEntryURI = \Qiniu\base64_urlSafeEncode('blog');
$parm = "/fetch/$EncodedURL/to/$EncodedEntryURI";
$token = $auth->sign("$parm\n");


echo 'EncodedURL = '.$EncodedURL."<br />";
echo 'EncodedEntryURI = '.$EncodedEntryURI."<br />";
echo 'token = '.$token."<br />";

$client = new Client();
$response = $client->get("http://iovip-z1.qbox.me$parm",[
    'headers'=>[
        'Authorization' =>"QBox $token"
    ]
]);

$result = $response->getBody();
echo $result;
/*{"fsize":34329,"hash":"Fkf3l424A8kf681zo_fxmWtGq8QO","key":"Fkf3l424A8kf681zo_fxmWtGq8QO","mimeType":"image/png"}*/