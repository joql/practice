<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/4/11
 * Time: 22:40
 */

require 'ApkParser.php';
require 'IpaParser.php';
var_dump($_FILES);
$main = new ApkParser;
$main->open($_FILES['app']['tmp_name']);
echo 'package: '.$main->getPackage()."\n";
/*echo 'version: '.$main->getVersionName()."\n";
echo 'code: '.$main->getVersionCode()."\n";
echo 'name: '.$main->getAppName()."\n";*/

