<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:02
 */

/**
 * use for: curl
 * @param $url
 * @param array $array
 * @param string $type
 * @return bool|mixed
 * auth: ksj
 * date:2017-10-10   15:20
 */
function curl($url,$array ,$type = 'post') {
    $ch = curl_init();
    if($type == 'get'){
        if(is_array($array)) {
            $query = http_build_query($array);
            $url = $url . '?' . $query;
        }
    }
    if(stripos($url, "https://") !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    if($type == 'post'){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
    }
    $content = curl_exec($ch);
    $status = curl_getinfo($ch);
    curl_close($ch);
    if(intval($status["http_code"]) == 200) {
        return $content;
    } else {
        echo $status["http_code"];
        return false;
    }
}

function postCurl($data, $url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
}

/**
 * use for: 靓号识别
 * @param $phone
 * @return bool
 * date:2017-09-13   10:09
 */
function getPhoneType($phone){

    $phone_rules = array(
        '尾号ABCDABCD'=>'/\d{3}((?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3}\d)\1/',
        '尾号AAAAAA	'=>'/\d{5}(\d)\1\1\1\1\1/',
        '尾号AAABBB	'=>'/\d{5}(\d)\1\1(\d)\2\2/',
        '尾号AABBCC	'=>'/\d{5}(\d)\1(\d)\2(\d)\3/',
        '尾号AAAAA	'=>'/\d{6}(\d)\1\1\1\1/',
        '尾号AABBB	'=>'/\d{6}(\d)\1(\d)\2\2/',
        '尾号ABCDE	'=>'/\d{6}(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){4}\d/',
        '尾号AABAA	'=>'/\d{6}(\d)\1(\d)\1\1/',
        '尾号AAAAB	'=>'/\d{6}(\d)\1\1\1(\d)/',
        '中间AAAA	'=>'/(\d)\1\1\1\d{1}/',
        '尾号AAAA	'=>'/\d{7}(\d)\1\1\1/',
        '尾号ABCD	'=>'/\d{7}(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3}\d/',
        '尾号AAAB	'=>'/\d{7}(\d)\1\1(\d)/',
        '尾号AABA	'=>'/\d{7}(\d)\1(\d)\1/',
        '尾号ABAA	'=>'/\d{7}(\d)(\d)\1\1/',
        '尾号ABBA	'=>'/\d{7}(\d)(\d)\2\1/',
        '尾号ABAB	'=>'/\d{7}(\d)(\d)\1\2/',
        '尾号ABC	'=>'/\d{8}(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){2}\d/',
        '中间AAA	'=>'/(\d)\1\1\d{1}/',
        '尾号AAA	'=>'/\d{8}(\d)\1\1/',
        '尾号AA		'=>'/\d{9}(\d)\1/'
    );

    foreach($phone_rules as $k =>$v){
        if(preg_match($v,$phone)){
            return $k;
        }
    }
    return false;
}

function intToMoney(& $number){
    $number= $number/100;
}

function xmlToArray($xml){
    //将XML转为array
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}

/**
 * use for:json格式化返回
 * @param $code
 * @param string $msg
 * @param array $data
 * @return string
 * date:2017-09-12   17:10
 */
function returnAjax($code, $msg = '', $data = array()){
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(array('code' => $code, 'data' => $data, 'message' => $msg)));
}

/**
 * use for:下载文件
 * @param $filepath
 * user: ksj
 * date:2017-10-10 23:08
 */
function download($filepath){
    header('content-disposition:attachment;filename='.basename($filepath));
    header('content-length:'.filesize($filepath));
    readfile($filepath);
}