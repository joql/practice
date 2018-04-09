<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/3
 * Time: 11:10
 */

require '../../../init.php';


use GuzzleHttp\Client;

$client = new Client();
$list = array();
for ($i=1;$i<800;$i++){
    $url = 'http://www.wangmingdaquan.cc/nansheng/list_20_'.$i.'.html';
    $response = $client->get($url);
    if($response->getStatusCode() != 200){
        continue;
    }

    preg_match_all('/<p>(.{1,10})<\/p>/',$response->getBody(),$tmp);
    $list = arrayMergBy2($list,$tmp);
    unset($response);
    unset($tmp);
    echo 'page: '.$i.',success'."\n";
}
foreach ($list[1] as $v){
    $str .= $v."\r\n";
}
saveFile('name.txt',$str);
//var_dump($list);
