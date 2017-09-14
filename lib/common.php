<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:02
 */

/**
 * use for: curl请求
 * @param $url
 * @param string $type
 * @param string $request_data
 * @return array|mixed
 * date:2017-09-11 22:36
 */
function curlRequest($url, $type = 'post', $request_data = '') {
    $header = array(
        "Content-Type:application/x-www-form-urlencoded;charset=UTF-8",
        "Connection:Keep-Alive",
        'Accept:application/json',
    );

    $ch = curl_init();
    /* cURL settings */
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($type == 'post') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
    //return $data = empty($result) ? array(1) : json_decode($result, true);
}

/**
 * use for:
 * @param $url
 * @param $data
 * @param bool $is_xml
 * @param int $second
 * @return mixed|string
 * date:2017-09-12 22:00
 */
function postCurl($url,$data,$is_xml=false, $second=30){
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);

    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if($is_xml){
        $header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //运行curl
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        return $data;
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        return "CURL_ERROR";
    }
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

