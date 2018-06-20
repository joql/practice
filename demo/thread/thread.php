<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/6/19
 * Time: 15:57
 */
require '../../init.php';

error_reporting(E_ALL&~E_NOTICE);
global $db;


/*
 *  第一步：创建任务 初始化任务状态
 *  第二步：执行任务 任务状态改为1
 *  第三步：子任务中根据标志修改任务状态为完成
 */

define("TS",50);//线程数
define("OT",180);//超时时间

$task_list = [];
//清空任务表
$db->delete('task_list');

//创建任务
for ($i=100;$i<4000;$i++){
    $task_list[] = [
        'task'=>$i
    ];
}

$db->insertMulti('task_list',$task_list);
//var_dump($db->where('state=1')->groupBy('state')->get('task_list',null,'count(id) as num'));
//die();
//执行任务
while (true){
    //线程上限
    $working = $db->where('state=1')->groupBy('state')->get('task_list',null,'count(id) as num');
    if($working[0]['num'] >= TS){
        $db->where('state = 1 and unix_timestamp(now()) - time > '.OT)->update('task_list',['state'=>3]);
        console('waiting, working '.$working[0]['num']);
        sleep(15);
        continue;
    }
    //执行完跳出
    $worked = $db->where('state=0')->groupBy('state')->get('task_list',null,'count(id) as num');
    if(empty($worked[0]['num'])){
        console('over');
        break;
    }
    $room_no = $db->where('state=0')->get('task_list',1);
    if(!empty($room_no[0]['task']) && $db->where('task='.$room_no[0]['task'])->update('task_list',['state'=>1,'time'=>time()],1)){
        console('task '.$room_no[0]['task'].' start, working '.$working[0]['num']);
        $cmd = 'cd ../yueru && start /B php ./yueru.php '.$room_no[0]['task'];
        //$cmd = 'cd ../yueru && start dir';
        run($cmd);
    }
    sleep(1);

}





/**
 * use for:cmd 执行
 * auth: Joql
 * @param $cmd
 * date:2018-06-19 17:10
 */
function run($cmd){
    if(strtoupper(substr(PHP_OS,0,3)) == 'WIN') {
        pclose(popen($cmd, 'r'));
    }else {
        pclose(popen($cmd.' > /dev/null &', 'r'));
    }
}

