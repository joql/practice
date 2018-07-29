<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/7/28
 * Time: 13:35
 */

require '../../init.php';

use GuzzleHttp\Client;


if(!empty($argv[1])){
    $act = $argv[1];
}
if(!empty($_GET['act'])){
    $act = $_GET['act'];
}

switch ($act){

    case 'checkUser':
        checkUser();
        break;
    default:
        returnAjax(0,'denined');
}

function checkUser(){
    $url='https://www.17ce.com/sitex/checkuser';

    $p_url = $_POST['url'];
    $p_type = $_POST['type'];
    $p_isp = $_POST['isp'];
    $client = new Client();
    $response = $client->post($url,[
        'verify' => false,
        'headers' => [
            'Referer' => 'https://www.17ce.com/'
        ],
        'form_params' => [
            'isp' => $p_isp,
            'type' => $p_type,
            'url' => $p_url
        ]
    ]);
    if($response->getStatusCode() != 200) returnAjax(0,'err');
    $result = $response->getBody();
    returnAjax(1,'success',json_decode($result,true));
}
