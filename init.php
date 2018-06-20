<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 21:49
 */

error_reporting( E_ALL&~E_NOTICE );
define('ROOT',dirname(__FILE__));
define('LIB_ROOT',dirname(__FILE__).'/lib/');

require LIB_ROOT.'common.php';
require 'vendor/autoload.php';

//header("content-type:text/html;charset=utf-8");
//require LIB_ROOT.'curl.class.php';
//header("Content-type:text/html;charset=utf-8");

//******连接数据库************
$db  = new MysqliDb([
    'host'      =>'localhost',
    'username'  =>'root',
    'password'  =>'admin001',
    'db'        =>'practice',
    'port'      =>3306,
    'prefix'    =>'p_',
    'charset'   =>'utf8'
]);
if(!$db) die("Database error");
//*******   end **************
