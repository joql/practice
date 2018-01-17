<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/1/17
 * Time: 21:51
 */

//*************** gd_info
//var_dump(gd_info());

//*************** imagearc
// 创建一个 200X200 的黑色背景图像
/*$img  =  imagecreatetruecolor ( 200 ,  200 );
// 分配颜色
$white  =  imagecolorallocate ( $img ,  255 ,  255 ,  255 );
$black  =  imagecolorallocate ( $img ,  0 ,  0 ,  0 );
// 画一个黑色的圆
imagearc ( $img ,  100 ,  100 ,  150 ,  150 ,  0 ,  360 ,  $white );
// 将图像输出到浏览器
header ( "Content-type: image/png" );
imagepng ( $img );
// 释放内存
imagedestroy ( $img );*/

//*************** imagetruecolortopalette
// Create a new true color image
/*$im  =  imagecreatetruecolor ( 100 ,  100 );

// Convert to palette-based with no dithering and 255 colors
imagetruecolortopalette ( $im ,  false ,  155 );

// Save the image
imagepng ( $im ,  './paletteimage.png' );
imagedestroy ( $im );*/

//******************
header ( "Content-type: image/jpeg" );
$im   =  imagecreatetruecolor ( 100 ,  100 );
$w    =  imagecolorallocate ( $im ,  255 ,  255 ,  255 );
$red  =  imagecolorallocate ( $im ,  255 ,  0 ,  0 );

/* 画一条虚线，5 个红色像素，5 个白色像素 */
$style  = array( $red ,  $red ,  $red ,  $red ,  $red ,  $w ,  $w ,  $w ,  $w ,  $w );
imagesetstyle ( $im ,  $style );
imageline ( $im ,  0 ,  0 ,  100 ,  100 ,  IMG_COLOR_STYLED );

/* 用 imagesetbrush() 和 imagesetstyle 画一行笑脸 */
$style  = array( $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $w ,  $red );
imagesetstyle ( $im ,  $style );

$brush  =  imagecreatefrompng ( "http://www.libpng.org/pub/png/images/smile.happy.png" );
$w2  =  imagecolorallocate ( $brush ,  255 ,  255 ,  255 );
imagecolortransparent ( $brush ,  $w2 );
imagesetbrush ( $im ,  $brush );
imageline ( $im ,  100 ,  0 ,  0 ,  100 ,  IMG_COLOR_STYLEDBRUSHED );

imagejpeg ( $im );
imagedestroy ( $im );