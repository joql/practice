<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 10:39
 */

//hash=lKoKHZmu&act=test
$str = 'hash=lKoKHZmu&act=getAccessToken';

echo '明文：'.$str;
echo '<br />';

$w = authcode($str,'ENCODE','nygzh');
//$w ='0bbbsjzaM1+QCNUrmpHXtTH3P+VGspybCcq0wbM9VubXhiO/e/mfMnP6tgH9twMXjx1l';
echo '加密后：'.$w;
echo '<br />';
echo '加密后(url编码后)：'.urlencode($w);
echo '<br />';
echo '解密后：'.authcode($w,'DECODE','nygzh');
echo '<br />';
echo '请求格式：?code='.urlencode($w);


/**
 * use for:
 * auth: Joql
 * @param $string
 * @param string $operation
 * @param string $key
 * @param int $expiry
 * @return bool|string
 * date:2018-01-26 10:41
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : $GLOBALS['_W']['config']['setting']['authkey']);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }

}
