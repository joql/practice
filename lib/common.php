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
function curl($url, array $array = array(), $type = 'get')
{
    $ch = curl_init();
    if ($type == 'get') {
        if (is_array($array) && is_null($array)) {
            $query = http_build_query($array);
            $url = $url . '?' . $query;
        }
    }
    if (stripos($url, "https://") !== false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($type == 'post') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
    }
    $content = curl_exec($ch);
    $status = curl_getinfo($ch);
    curl_close($ch);
    if (intval($status["http_code"]) == 200) {
        return $content;
    } else {
        return $status["http_code"];
    }
}

/**
 * TODO: CURL模拟POST提交
 * @description PHP模拟网页post提交
 * @author AriFe.Liu
 * @time 2017年3月21日10:21:08
 * @param data 要发送的数据，可以为xml或数组
 * @param url  String 请求地址
 * @param is_xml Bool 是否是xml数据(xml数据需要设定特定的包头信息，所以如果提交xml数据，需要将此参数设为true)
 * @param second Int 等待超时时间，默认30s
 * @return 远端返回的响应信息
 * */
function postCurl($url, $data, $is_xml = false, $second = 30)
{
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//严格校验
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if ($is_xml) {
        $header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //运行curl
    $data = curl_exec($ch);
    //返回结果
    if ($data) {
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
function getPhoneType($phone)
{

    $phone_rules = array(
        '尾号ABCDABCD' => '/\d{3}((?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3}\d)\1/',
        '尾号AAAAAA	' => '/\d{5}(\d)\1\1\1\1\1/',
        '尾号AAABBB	' => '/\d{5}(\d)\1\1(\d)\2\2/',
        '尾号AABBCC	' => '/\d{5}(\d)\1(\d)\2(\d)\3/',
        '尾号AAAAA	' => '/\d{6}(\d)\1\1\1\1/',
        '尾号AABBB	' => '/\d{6}(\d)\1(\d)\2\2/',
        '尾号ABCDE	' => '/\d{6}(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){4}\d/',
        '尾号AABAA	' => '/\d{6}(\d)\1(\d)\1\1/',
        '尾号AAAAB	' => '/\d{6}(\d)\1\1\1(\d)/',
        '中间AAAA	' => '/(\d)\1\1\1\d{1}/',
        '尾号AAAA	' => '/\d{7}(\d)\1\1\1/',
        '尾号ABCD	' => '/\d{7}(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3}\d/',
        '尾号AAAB	' => '/\d{7}(\d)\1\1(\d)/',
        '尾号AABA	' => '/\d{7}(\d)\1(\d)\1/',
        '尾号ABAA	' => '/\d{7}(\d)(\d)\1\1/',
        '尾号ABBA	' => '/\d{7}(\d)(\d)\2\1/',
        '尾号ABAB	' => '/\d{7}(\d)(\d)\1\2/',
        '尾号ABC	' => '/\d{8}(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){2}\d/',
        '中间AAA	' => '/(\d)\1\1\d{1}/',
        '尾号AAA	' => '/\d{8}(\d)\1\1/',
        '尾号AA		' => '/\d{9}(\d)\1/'
    );

    foreach ($phone_rules as $k => $v) {
        if (preg_match($v, $phone)) {
            return $k;
        }
    }
    return false;
}

function intToMoney(& $number)
{
    $number = $number / 100;
}

function xmlToArray($xml)
{
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
function returnAjax($code, $msg = '', $data = array())
{
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode(array('code' => $code, 'data' => $data, 'message' => $msg)));
}

/**
 * use for:下载文件
 * @param $filepath
 * user: ksj
 * date:2017-10-10 23:08
 */
function download($filepath)
{
    header('content-disposition:attachment;filename=' . basename($filepath));
    header('content-length:' . filesize($filepath));
    readfile($filepath);
}

/**
 * use for:文件写入
 * @param $path
 * @param $str
 * @return bool
 * user: ksj
 * date:2017-10-11 20:47
 */
function saveFile($path, $str)
{
    $file_hand = fopen($path, 'ab');
    if (!$file_hand) return false;
    if (!is_writable($path)) return false;
    if (fwrite($file_hand, $str) && fclose($file_hand)) return true;
    return false;
}

/**
 * use for:合并2维数组
 * auth: Joql
 * @param $old
 * @param $new
 * @return mixed
 * date:2018-03-14 23:23
 */
function arrayMergBy2($old, $new)
{
    if (empty($old)) return $new;
    foreach ($old as $k => $v) {
        foreach ($new[$k] as $v) {
            array_push($old[$k], $v);
        }
    }
    return $old;
}

/**
 * use for:导出 csv
 * auth: Joql
 * @param $data
 * @param null $header
 * @param string $fileName
 * date:2018-06-17 10:56
 * type 1 web下载  2 自动保存
 */
function excel_export_data($data, $header=null, $fileName='example.csv',$type = 1) {

    // 如果手动设置表头；则放在第一行
    if (!is_null($header)) {
        array_unshift($data, $header);
    }



    //输出10MB大小数据到是保存在内存中，如果它超过设置值，则自动写入临时文件
    $csv = fopen('php://temp/maxmemory:' . (100 * 1024 * 1024), 'r+');

    //添加BOM来修复UTF-8乱码
    fputs($csv, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
    foreach ($data as $v) {
        fputcsv($csv, $v);
    }
    rewind($csv);
    if($type == 1){
        //header设置
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition:filename=' . $fileName);
        exit(stream_get_contents($csv));
    }else{
        $file_open = fopen($fileName,'w');
        fwrite($file_open,stream_get_contents($csv));
        fclose($file_open);
    }
}
//二