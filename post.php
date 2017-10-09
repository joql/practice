<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/25
 * Time: 20:08
 */

function http_post_json($url, $jsonStr)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        )
    );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $response;
    return array($httpCode, $response);
}

$url = "http://p.cn/test.php";
$jsonStr = json_encode(array('a' => 1, 'b' => 2, 'c' => 2));
echo http_post_json($url, $jsonStr);