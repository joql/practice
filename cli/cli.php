<?php
/**
 * Created by PhpStorm.
 * User:ksj
 * Date: 2017/10/11
 * Time: 11:44
 */
require '../init.php';

header('content-type:text/html;charset=gb2312');
$count = 0;
do{
    $arr_tels = array();//手机号数组
    $str = '';//写入字符串
    $count +=20;
    $url = 'http://www.zzhmw.com/ydxh.asp?offset='.$count;
    $res = curl($url);
    preg_match_all('/1[34578]\d{9}/',$res,$arr_tels);
    foreach ($arr_tels[0] as $k=>$v){
        $str .= $v."\n";
    }
    if(!saveFile('save.txt',$str)) echo '文件写入失败';
    sleep(3);
}while($count<=1000);

