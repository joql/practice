<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/1/26
 * Time: 23:40
 */
require '../../init.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('your.log', Logger::WARNING));

// add records to the log
$log->warning('Foo');
$log->error('Bar');