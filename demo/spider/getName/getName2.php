<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/9
 * Time: 11:10
 */

require '../../../init.php';

$file_str=fopen('name.txt', 'r');
$body = fread($file_str,filesize('name.txt'));
fclose($file_str);

$line = explode("\r\n", $body);
$count_find = 0;
foreach ($line as $v){
    $name = explode('	',$v);
    $str = mb_convert_encoding($name[1],'UTF-8','GB2312');
    preg_match_all('/[\x{4e00}-\x{9fff}]+/u',$str,$matches);
    $str = join('',$matches[0]);
    $str = mb_convert_encoding($str,'GB2312','UTF-8');
    if(mb_strlen($str) > 4 && mb_strlen($str) <=7){
        $count_find ++;
        echo $count_find.': '.$str."\n";
        $list[] = $str;
    }
}

//save to file
$count = count($list);
echo "total : $count \n";
for($i=0;$i<$count;$i++){
    if($i%1000 === 0){
        if(!empty($save)){
            foreach ($save as $v){
                $str .= $v."\r\n";
            }
            echo 'the '.($i/1000).' is save'."\n";
            saveFile('./save.txt',$str);
        }
        unset($save);
        unset($str);
    }else{
        $save[] = $list[$i];
    }
}