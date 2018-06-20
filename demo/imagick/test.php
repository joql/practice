<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:08
 */

require 'init.php';
error_reporting(E_ALL);

//仅能在cli模式下运行

$s = pdf2png('public/uploads/132.pdf','./');
echo '<div align="center"><img src="'.$s.'"></div>';

function pdf2png($pdf,$path,$page=0)
{
    if(!is_dir($path))
    {
        mkdir($path,true);
    }
    if(!extension_loaded('imagick'))
    {
        echo '没有找到imagick！' ;
        return false;
    }
    if(!file_exists($pdf))
    {
        echo '没有找到pdf' ;
        return false;
    }
    $im = new Imagick();
    $im->setResolution(120,120);   //设置图像分辨率
    $im->setCompressionQuality(80); //压缩比
    $im->readImage($pdf."[".$page."]"); //设置读取pdf的第一页
    //$im->thumbnailImage(200, 100, true); // 改变图像的大小
    $im->scaleImage(200,100,true); //缩放大小图像
    $filename = $path."/". time().'.png';
    if($im->writeImage($filename) == true)
    {
        $Return  = $filename;
    }
    return $Return;
}