<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/26
 * Time: 12:04
 */

if(move_uploaded_file($_FILES['voice']['tmp_name'],'./'.$_FILES['voice']['name'].'.mp3')){
    exit('ok');
}
echo 'fail';