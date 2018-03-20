<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/19
 * Time: 22:21
 */
//1.png 61   2.png 52

$str = 'data:image/png;charset=utf-8;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAWCAYAAAC2ew6NAAADyklEQVRIx+2Wa1DUVRiHnz+FEAQYAsM6xrZiFEkocllEaRyFWAox0TR1WJUBQ0dFcxQVL2iJXWQyyi44U0gUKHcBxcoyCYxgwYJgYDIuC4JcLRTY5bJ9cpmtYdi1vjjxfju/9z1nnnnP+b1zBN/FL2p4AMKIByQmQf+3oA+PlwgM8OPlFcsQO8ygq7uHjKzzpGfm6tRMnWpFTPROpF4eyMM209ikBMDH25MN8rVIJGK6u7r5Ii2TvIJCnb0hLwWxMiQYW1sbmpuVJCWnUlT847igD4klTrF/F59ymsWBfbvIzbvAucwcBlUqwjeGcrOtnRu/NwDg6eFG/FtHcZwpQRAEsnMLuP3HnyzwkRJ39ACXrxRx5vM0RkZHeTV8Pc3KFhoamwB4QebPa1FbSM/K5Wx6NubmZkRGbERR+TMdHZ36d7Su/jdWrQujv38AgLLyStzd5rB4kS+Xvv4Wl9nOvHP8CD+UlFJy7Sf27o7S7i0rq+BgbJy2O4qK67i7uRIcJOPyd1cBkD2/hLLyCpJTzmrPl3q6479kEVXVNYZd/T3IeyEIAgODgwBU/1pLzOFjFJeU4i310KlTDw394wo7OrsR2duNGcNIQKUe0qlRqYcYGh6+fzOZmT3CyuVLETvM4FxGjlYvLinV2wgS8eO0tLZp198XleAz34vAAD8EQWCBjxQ722nkF1wy3EwA89xcOXkiDpVaTcIHidTU1hns1nlurkyfLuLj00laLT0zlyfEDuzbs4OIsFAsLC2I3h+rfcMGd7S+/gaRW3fxSeJnbN+6ibWrVxgEOcXYmJ3bN1NVXcOVq8Va3Vvqge9Cb96OTyAlNZ2enl5iD0bj9KTj/YHeuXuXmto6MrLzuFj4DRvkawwCjdoWibX1Y7wed0LnKR2O2cP5/ELyL3xFVk4+8rAtdHX1sHf3jn8/8G91dGJqaoqFxaN61S9bGkigzI9DR96k/VaHVnecKcHc3IxfqsbcrVKpKCq+xixHCSYmJvqDTjE2RiSy19Gcn3ait/c2fX13JoScO+dZorZF8u57H6GouK6T6+vrA9CZAgB2tjb0DwygVqv1N5M89BWCg2R8mvQlypZWpF7uPLdwPu9/eHpCSJHInjdi96NQVNLQ2ITLbGdtrra2jsYmJeWKSiLC5AiCEcqWVua6uhAY4MeZlDQ0Go3+oEnJqWhGNaxZHYLNNGtutrUTf/IUuXkXJwR1dXkGS0sLpF4eSL3GZuzwyAj+suUAxBw6xqbw9cjXrcLKypKW1jYSTiWSlZM/7rnC5Md5EnQS9L+JvwDL4WFeZnk7jAAAAABJRU5ErkJggg==';
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
imagefilter($im, IMG_FILTER_GRAYSCALE);
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
/*for ($i = 0; $i < $hid; $i ++) {
    for ($j = 0; $j < $wid; $j ++) {

        if ($rgbArray[$i][$j]['red'] == $rgbArray[0][0]['red']) {
            echo '□';
        } else {
            echo '■';
        }
    }
    echo "<br>";
}*/
for ($i = 5; $i < $hid-5; $i ++) {
    for ($j = 5; $j < $wid-5; $j ++) {
        if ($rgbArray[$i][$j]['red'] == $rgbArray[0][0]['red']) {
            echo '□';
        } else {
            echo '■';
        }
    }
    echo "<br>";
}
$widths = array();
for($i = 5; $i < $wid - 5; $i++){
    for($j =5 ,$cout=0; $j < $hid - 5; $j++,$cout++){
        if( ($rgbArray[$j][$i]['red'] != $rgbArray[0][0]['red']) && ($rgbArray[$j][$i+1]['red'] != $rgbArray[0][0]['red'])){
            break;
        }
        if($cout >= 11){
            $widths[] = $i;
            $i++;
        }
    }
}
var_dump($widths);
$start=5;
for ($i = 0;$i < count($widths);$i++){
    echo "第".($i+1)."个字<br>";
    for ($j = 0; $j < 12; $j++){
        for ($k = $start;$k <= $widths[$i];$k++){
            if ($rgbArray[$j+5][$k]['red'] == $rgbArray[0][0]['red']) {
                echo '0';
            } else {
                echo '1';
            }
        }
    }
    $start = $widths[$i]+1;
    echo "<br>";
}

