<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 15:30
 */

require_once dirname(__FILE__).'/../../vendor/chrisboulton/php-resque/lib/Resque.php';

//Queue:: in('push','PHP_Job',['push'=>'123']);

class Queue
{
    public static $queue_name;
    public static $job_name;
    public static $args;

    public static function in($queue_name = 'default',$job_name,$args){
        if(empty($job_name)){
            return false;
        }
        self::$queue_name = $queue_name;
        self::$job_name = $job_name;
        self::$args = $args;

        Resque::setBackend('127.0.0.1:6379');

        $jobId = Resque::enqueue($queue_name,$job_name, $args, true);
        return $jobId;
    }
}

