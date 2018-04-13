<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/11
 * Time: 22:40
 */

require 'IpaParser.php';
require 'ApkParser.php';

error_reporting(E_ALL);

if(!empty($_FILES)){
	$filepart = pathinfo($_FILES['app']['name']);
	if(strtolower($filepart['extension']) == 'ipa'){
		$dir = './data/';
		$name = time().rand(111,999).'.ipa';
		@move_uploaded_file($_FILES['app']['tmp_name'], $dir.$name);
		$ipa = new IpaParser($dir, $name, $dir);
		$ipa->handle();
	}elseif (strtolower($filepart['extension'] == 'apk')){
		$apk = new ApkParser();
		$apk->open($_FILES['app']['tmp_name']);
		echo $apk->getAppName();
		echo $apk->getPackage();
		echo $apk->getIcon();
		echo $apk->getVersionName();
		echo $apk->getVersionCode();
	}
}
?>

