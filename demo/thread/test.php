<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/6/19
 * Time: 15:52
 */

$tag = $argv[1];

sleep(4);
$hand = fopen('test.txt','a+');
fwrite($hand,date('Y-m-d H:i:s',time()).' '.$tag."\n");
fclose($hand);