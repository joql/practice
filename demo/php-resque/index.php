<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/1/26
 * Time: 23:59
 */
require_once 'queue.php';

Queue:: in('push','PHP_Job',['push'=>'123']);