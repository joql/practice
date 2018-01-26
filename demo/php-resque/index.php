<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/1/26
 * Time: 23:59
 */
require_once 'queue.php';

$test = new test();
Queue:: in('push','PHP_Job',['push'=>$test]);

class test
{
    public $a;
    public $b;
    function test(){
        $a =1;
        $b = 2;
        return $a.$b;
    }
}