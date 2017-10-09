<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/10/9
 * Time: 22:40
 */

//注意 中文名 会出错【使用iconv 可解决此问题】
$file = $_FILES['my_file'];

/*'  file 内容
    my_file' =>
    array (size=5)
      'name' => string '故宫收款改版.jpg' (length=22)
      'type' => string 'image/jpeg' (length=10)
      'tmp_name' => string 'C:\Windows\php4D38.tmp' (length=22)
      'error' => int 0
      'size' => int 125025
*/
$name = $file['name'];
$type = $file['type'];
$tmp_name = $file['tmp_name'];
$size = $file['size'];
$err_code = $file['error'];

//检测图片 并获取图片信息  getimagesize 可获取图片信息
if(!is_array(getimagesize($tmp_name))) die();

//检测文件是否是post       is_uploaded_file()
//获取文件名后缀
$ext = pathinfo($file['name'],PATHINFO_EXTENSION);

//保存图片
$save_path = './../public/uploads/'.uniqid(microtime(true),true).'.'.$ext;
//if($err_code == UPLOAD_ERR_OK) echo move_uploaded_file($tmp_name,'./../public/uploads/'.iconv("UTF-8", "GBK", $name));
if($err_code == UPLOAD_ERR_OK) echo move_uploaded_file($tmp_name,$save_path);

