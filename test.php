<?php

require 'init.php';

$arr = [];
for($i=0;$i<10000;$i++){
	$arr[] = $i;
}

$start = microtime();
echo 'start '.$start."\n";

foreach ($arr as $key => $value) {
	# code...
	$value =2;
}
$end = microtime();
echo 'end '.$end."\n";
echo 'count '.count($arr)."\n";
echo 'runTime '.($end-$start)."s\n";

unset($arr);
$arr = [];
for($i=0;$i<100;$i++){
	$arr[] = $i;
}

$start = microtime();
echo 'start '.$start."\n";

foreach ($arr as $key => $value) {
	# code...
	$value =2;
}
$end = microtime();
echo 'end '.$end."\n";
echo 'count '.count($arr)."\n";
echo 'runTime '.($end-$start)."s\n";