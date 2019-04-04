<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2019/4/4
 * Time: 11:58
 */

require_once '../../init.php';

$csv = new \Keboola\Csv\CsvReader('./1.csv');

foreach($csv as $row) {
    var_dump($row);
}
