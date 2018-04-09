<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/23
 * Time: 9:13
 */


//var_dump($_FILES['voice']);

if(move_uploaded_file($_FILES['voice']['tmp_name'],'./'.$_FILES['voice']['name'].'.mp3')){
    exit('ok');
}
echo 'fail';

/*$arr = get_defined_vars();
var_dump($arr);*/