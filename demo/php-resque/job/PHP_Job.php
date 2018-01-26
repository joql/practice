<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 15:29
 */
require dirname(__FILE__).'/../../../init.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class PHP_Job
{
    public function perform()
    {
        $push = $this->args['push'];
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('your.log', Logger::WARNING));

        // add records to the log
        $log->warning('push: '.$push);
        echo $push;
    }
}