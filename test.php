<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:08
 */
require './init.php';
/*$arr = [123000];
$temp = 123000;
intToMoney($arr[0]);
intToMoney($temp);
var_dump($arr);
echo $temp;*/
/*require 'vender/phpqrcode/qrlib.php';

QRcode::png('http://b.kylinqi.cn');*/

/*$xml = <<<XML
        <xml>
<appid><![CDATA[wx2421b1c4370ec43b]]></appid>
          <attach><![CDATA[scenicpay_1_123_123_1]]></attach>
          <bank_type><![CDATA[CFT]]></bank_type>
          <fee_type><![CDATA[CNY]]></fee_type>
          <is_subscribe><![CDATA[Y]]></is_subscribe>
          <mch_id><![CDATA[10000100]]></mch_id>
          <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
          <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
          <out_trade_no><![CDATA[1409811653]]></out_trade_no>
          <result_code><![CDATA[SUCCESS]]></result_code>
          <return_code><![CDATA[SUCCESS]]></return_code>
          <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
          <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
          <time_end><![CDATA[20140903131540]]></time_end>
          <total_fee>1</total_fee>
          <trade_type><![CDATA[JSAPI]]></trade_type>
          <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
        </xml>
XML;
$pay = array(
    'body'=>'景区门票',
    'total_free'=>'123',
    'out_trade_no'=>'213',
    'attach'=>'scenicpay_1_123_123_1'
);
$res = curl('http://pay.cn/mobile.php/Home/Notify/scenicpay',$xml,'post');
//$res = postCurl($xml,'http://pay.cn/notify.php');
echo $res;
//var_dump(xmlToArray($xml));
//download('./init.php');*/
returnAjax(1,'success');
