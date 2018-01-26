<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 17:17
 */

//自动加载
spl_autoload_register(function ($class_name){
    require_once dirname(__FILE__).'/job/'.$class_name.'.php';
});

require_once dirname(__FILE__).'/../../vendor/chrisboulton/php-resque/lib/Resque.php';
require_once dirname(__FILE__).'/../../vendor/chrisboulton/php-resque/lib/Resque/Worker.php';

$QUEUE = getenv('QUEUE');
if(empty($QUEUE)){
    die('please set QUEUE');
}

Resque::setBackend('127.0.0.1:6379');
