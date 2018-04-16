<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/16
 * Time: 15:23
 */


$act = $argv[1];
switch ($act){
    case 'parse-app':
        parseApp();break;
}

function parseApp(){
    exec('D:\tool\apk-tools\aapt d badging test.apk 2>&1', $out, $resutl);
    var_dump($out);
}