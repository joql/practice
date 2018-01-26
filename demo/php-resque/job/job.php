<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 15:29
 */
require '/init.php';
class PHP_Job
{
    public function perform()
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('your.log', Logger::WARNING));

        // add records to the log
        $log->warning('Foo');
        $log->error('Bar');
    }
}