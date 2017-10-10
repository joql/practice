<?php
/**
 * Created by PhpStorm.
 * User:ksj
 * Date: 2017/10/10
 * Time: 14:18
 */
require '../init.php';

$post = $_POST;
$data = array();
foreach ($post['key'] as $k=>$v){
    $data[$v] = $post['val'][$k];
}
$res = curl($post['url'],$data);
returnAjax(1,$res);