<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/2/5
 * Time: 23:40
 */

copy('1.ipa','1.zip');
$zip = new ZipArchive;
$res = $zip->open('1.zip');
if($res === true){
    echo 'ok';
    $zip->extractTo('1');
    $zip->close();
}else{
    echo 'failed,code='.$res;
}


