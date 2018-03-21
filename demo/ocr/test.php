<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/21
 * Time: 11:12
 */

require '../../init.php';
require 'ocr.php';


use GuzzleHttp\Client;

$client = new Client();

$response = $client->get('https://www.proxyrotator.com/free-proxy-list/1/',[
    'verify'        => false,
    'proxy'         =>'http://127.0.0.1:1080'
]);

if($response->getStatusCode() != 200) exit();
preg_match_all('/img src=\"(data.*)\"/',$response->getBody(),$result);


foreach ($result[1] as $v){
    $ocr = new Ocr();
    $ocr->init($ocr->savePicByBase54($v,'../../public/img/ocr/',microtime()),[5,6,5,6,2]);
    //$ocr->test();
    echo 'result: '.$ocr->recognition().'<br>---------------------------------------<br>';
    //unset($ocr);
}