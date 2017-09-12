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


