<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/19
 * Time: 22:21
 */
//1.png 61   2.png 52

$str = 'data:image/png;charset=utf-8;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAWCAYAAAC2ew6NAAACFUlEQVRIx+2WT0iTcRjHP+8IRhOdUI46lNoqqFZUzLa0QaSmVofo0CVcMNJDQXhJa8NciAiaRIqHjIhk9AdpWLyVGR5KthBXQqVWlxSVDo51GCTbhHUzhu/G229dhPd7/D3P78PnfXgf+EmOYyeTrIHoWCPRRDVRpZw5fYqH/X28eeXn3p3bOMrsq3ry8410tnt5NyJTVLhFdU0NW5XoiepKLl+qZ2h4BHdzK9Nfv9PqdbPXsnulp8R6gPt3eyixHlx1P1NNDVu1aPXxcsZDH+n3PWE8NMHNW70sLoapLD8KgGXPLjrbbzA59Y2Orp6Uu5lqathKWZf2C3QSsXgi5SwWT5BYXgbgy+Q0npY2AsEx7DZrSl+mmhr2P0307WiQ0sOHqKmqQJIkykptmAo2IL94vdITCI6lBWeqqWGrnujA02cUFW7lWmMDda5acvNyaXJ7+TEzm/UGi7DTTtRus+I4YqejqxvfowEikV94m5vYucOctagIW1HUYFhPi6eR5/IQ8sth/IMyTtdFwuEIV680ZCUpylYUNW8rJifHwKfPU39/9liM0cB7tpuL0ev1wqKibEXRaDQKwOZNppRzU8FGfi8tEY/HhUVF2YrLNDM7R+jDBHUuJ5KkY25+gf37LNRUVfDA95hkUvxlKMpOu/We623UXziP89xZjMY85hd+0t3bh39QznqZRNiS9nDWRDXR/5M/LLokmD7ey7IAAAAASUVORK5CYII=';
//$str ='data:image/png;charset=utf-8;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAWCAYAAAC/kK73AAACv0lEQVRIx+2WW0iTYRyHn0+rTbfMC91yuqxlddFBMrWGImJlJ0mEViHdRNFBJSSzoDRHiYZ2vugmCkOiAx00MS+CqKjQ2Tx0lKBSs2lOxbTpDppdBKOVyXfxRQT73b3v9/8/PLwf70GI1q8Y4z+MD/9pvOJecZGZNNHHjRtS2WxIQ6UKprWtnfMXy3n46KlHTZw+lqyM7WjDQvnQ2sbxU+dofv7yr3BErfj6lNXkZGdQXXOPnAOHefW6hZKiAiIXLXDXzNbNpPSYkTctb9m7Px9LZxdnTxahVqsk54gWX7dmJbV1z7hQdpk6k5nikjN0d1tZnZzkrtmSbsDa08vRohOY6hvINxbjcrnYtCFVco5ocR8fAafT5THncLoYGRlxj5fGRGGqb2Bs7MdV4HS6MDc2s2xptOQc0eL3HzwmPm4ZKWuTEQSBhHg9alUQFXfuAiCTyQgODsLS2eXRZ+n8jDZMIzlH9Oa8cu0WulnhFOTlkrFzKwEBAWTvO8S7960ATJ2qBMBmG/Los9lsyOVyfH19GB39JhlH9IrH6WNJTIijsPgkZeVX6e3ro/hoHvPmRnjUCYIw4bElFUeUuMLfn8IjB7lVUU1lVQ3Xb1SyMX071p5eCvJyARgc/AqAv7+fZ69Cgd1uZ3T0m2Qc0eIRETqUCgVNP52jDoeDBw+fMCdCh0wmw+FwYLX2oAmZ7tGrCVHT1t4hKUe0+MDAgLv556hUQQwNDeN0OgGoNZmJjYly/+YpUyazZHEkpvoGSTnjxVej1Rl/nezv/0LkwvmsWbWcoWE7SqWClHXJbDakcan8GuaGZgA6PlnYkm4gVBPCsN1O5u5thM/QYiwsxWazScYZL8Kf3uN+fnIyd20jKTGewMBpfOywcPN2FddvVP62+fZk7SBUE0JrWzsnTp+jsemF5BzR4t7XoVfcK/5v8x3tnck1gGxM4wAAAABJRU5ErkJggg==';
if (preg_match('/^(data:\s*image\/(\w+);charset=utf-8;base64,)/', $str, $result)) {
    $type = $result[2];
    $new_file = "./test.{$type}";
    if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $str)))) {
    }
}
$url =$new_file;
//$url='2.png';
//$color=52;

$im = imagecreatefrompng($url);
//imagefilter($im, IMG_FILTER_GRAYSCALE);
$rgbArray = array();
$res = $im;
$size = getimagesize($url);
echo '<img src="'.$url.'" /><br>';
$wid = $size['0'];
$hid = $size['1'];
echo "h:$hid,w:$wid<br>";
for ($i = 0; $i < $hid; ++ $i) {
    for ($j = 0; $j < $wid; ++ $j) {
        $rgb = imagecolorat($res, $j, $i);
        $rgbArray[$i][$j] = imagecolorsforindex($res, $rgb);
    }
}
//var_dump($rgbArray);
for ($i = 0; $i < $hid; $i ++) {
    for ($j = 0; $j < $wid; $j ++) {

        if ($rgbArray[$i][$j]['red'] <= 190 && $rgbArray[$i][$j]['red'] >= 10) {
            echo '□';
        } else {
            echo '■';
        }
    }
    echo "<br>";
}
for ($i = 5; $i < $hid-5; $i ++) {
    for ($j = 6; $j < $wid-5; $j ++) {
        if ($rgbArray[$i][$j]['red'] <= 190 && $rgbArray[$i][$j]['red'] >= 10) {
            echo '□';
        } else {
            echo '■';
        }
    }
    echo "<br>";
}
$widths = array();


for ($i = $wid-6; $i>5; $i--){
    for($j =5,$count=0; $j <$hid-5; $j++){
        if(($rgbArray[$j][$i]['red'] <= 190 && $rgbArray[$j][$i]['red'] >= 10) && ($rgbArray[$j][$i-1]['red'] <= 190 && $rgbArray[$j][$i-1]['red'] >= 10)){
            $count++;
        }
        if($count >11){
            //echo 'w: '.$i.' h: '.$j.'<br>';
            $widths[] = $i -2;
            $i = $i -2;
        }
    }
}
var_dump($widths);
$widths = array_reverse($widths);
$last_count = 0;
for ($j=5;$j<$hid-5;$j++){
    if ($rgbArray[$j][$wid-6]['red'] <= 190 && $rgbArray[$j][$wid-6]['red'] >= 10) {
        $last_count++;
    }
}
//echo $last_count;
if($last_count > 11){
    $widths[] = $wid -7;
}else{
    $widths[] = $wid -6;
}
$start=6;
for ($i = 0;$i < count($widths);$i++){
    echo "第".($i+1)."个字<br>";
    for ($j = 0; $j < 12; $j++){
        for ($k = $start;$k <= $widths[$i];$k++){
            if ($rgbArray[$j+5][$k]['red'] <= 190 && $rgbArray[$j+5][$k]['red'] >= 10) {
                echo '□';
            } else {
                echo '■';
            }
        }
        echo "<br>";
    }
    $start = $widths[$i]+1+2;
    echo "<br>";
}

