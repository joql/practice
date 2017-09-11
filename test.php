<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:08
 */
require dirname(__FILE__).'/init.php';

echo curlRequest('http://www.baidu.com','get');