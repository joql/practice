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


