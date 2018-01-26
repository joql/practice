<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 15:29
 */
class PHP_Job
{
    public function perform()
    {
        sleep(120);
        fwrite(STDOUT, 'Hello!');
    }
}