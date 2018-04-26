<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/26
 * Time: 11:43
 */

require '../../init.php';


global $db;


$list = $db->get('juming_url_id_list');
returnAjax(1,'success',$list);