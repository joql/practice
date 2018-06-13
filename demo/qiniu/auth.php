<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/6/13
 * Time: 9:39
 */

require '../../init.php';


use GuzzleHttp\Client;
error_reporting(E_ALL);;

//重点 在zone.php文件中选择空间对应的服务器 如 华北 iovip-z1.qbox.me
$accessKey = '';
$secretKey = '';

$qiniu = new QiNiu($accessKey, $secretKey);
$data = $qiniu->fetch('blog','http://fanyi.baidu.com/static/webpage/img/download/spritesheet.png');


$client = new Client();
$response = $client->get("http://iovip-z1.qbox.me".$data[0],[
    'headers'=>[
        'Authorization' =>"QBox ".$data[1]
    ]
]);

$result = $response->getBody();
echo $result;
/*{"fsize":34329,"hash":"Fkf3l424A8kf681zo_fxmWtGq8QO","key":"Fkf3l424A8kf681zo_fxmWtGq8QO","mimeType":"image/png"}*/

class QiNiu
{
    public $accessKey;
    public $secretKey;

    public function __construct($accessKey, $secretKey){
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    public function fetch($bucket, $pic_url){
        $EncodedURL = $this->base64SafeEncode($pic_url);
        $EncodedEntryURI = $this->base64SafeEncode($bucket);
        $parm = "/fetch/$EncodedURL/to/$EncodedEntryURI";
        $token = $this->sign("$parm\n");
        return [$parm,$token];
    }

    public function sign($data){
        $hmac = hash_hmac('sha1', $data, $this->secretKey, true);
        return $this->accessKey . ':' . $this->base64SafeEncode($hmac);
    }

    private function base64SafeEncode($data){
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

}