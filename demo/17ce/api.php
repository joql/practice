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
    case 'addSite':
        addSite();
        break;
    case 'getSite':
        getSite();
        break;
    case 'delUrl':
        delUrl();
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
    try{
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
    }catch (Exception $e){
        try{
            $url='https://www.17ce.com/site/checkuser';
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
        }catch (Exception $e1){
            returnAjax(0,'网络异常');
        }
    }

    if($response->getStatusCode() != 200) returnAjax(0,'err');
    $result = $response->getBody();
    returnAjax(1,'success',json_decode($result,true));
}

function addSite(){
    global $db;
    (empty($_POST['type']) || empty($_POST['url']) || empty($_POST['name'])) && returnAjax(0,'err');
    $data = [
        'type' => $_POST['type'],
        'url' => $_POST['url'],
        'name' => $_POST['name'],
    ];

    $db->insert('17ce_url_list',$data);
    returnAjax(1,'success');
}
function getSite(){
    global $db;

    $data = $db->get('17ce_url_list');
    foreach ($data as $k=>$v){
        switch ($v['type']){
            case '1':
                $data[$k]['prot'] = 'http';
                break;
            case '2':
                $data[$k]['prot'] = 'https';
                break;
        }
    }
    returnAjax(1,'success',$data);
}


function delUrl(){
    global $db;
    (empty($_POST['id'])) && returnAjax(0,'err');
    $filter = [
        'id' => $_POST['id']
    ];

    $db->where('id='.$_POST['id'])->delete('17ce_url_list',1);
    returnAjax(1,'success');
}
