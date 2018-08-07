<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/7/28
 * Time: 13:35
 */

require '../../init.php';
require 'chinaz.php';

use GuzzleHttp\Client;



if(!empty($argv[1])){
    $act = $argv[1];
}
if(!empty($_GET['act'])){
    $act = $_GET['act'];
}

global $db;
$client = new Client();
$chinaz = new Chinaz($db, $client);

switch ($act){
    case 'updateHost':
        $chinaz->saveTestPoint();
        returnAjax(1,'success');
        break;
    case 'getTestPointResult':
        $chinaz->getTestPoint($_POST['host']);
        $data = $chinaz->getTestPointResult();
        returnAjax(1,'success',$data);
        break;
    case 'addSite':
        if($chinaz->addSite($_POST['type'], $_POST['url'], $_POST['name'])){
            returnAjax(1,'success');
        }else{
            returnAjax(0,'添加失败');
        }
        break;
    case 'getSite':
        $data = $chinaz->getSite();
        empty($data) ? returnAjax(0,'获取失败') : returnAjax(1,'success',$data);
        break;
    case 'delUrl':
        $chinaz->delUrl($_POST['id']) ? returnAjax(1,'success') : returnAjax(0,'删除失败');
        break;
    case 'test':
        returnAjax(1,'success',$chinaz->test());
        break;
    default:
        returnAjax(0,'denined');
}



