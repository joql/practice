<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/26
 * Time: 15:30
 */
require '../../init.php';
Resque::setBackend('127.0.0.1:6379');

$args = array(
    'time' => time(),
    'array' => array(
        'test' => 'test',
    ),
);

$jobId = Resque::enqueue('default', $argv[1], $args, true);
echo "Queued job ".$jobId."\n\n";