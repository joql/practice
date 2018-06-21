<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/6/21
 * Time: 15:15
 */
require '../../init.php';


//pthread 测试


//任务类
class doing
{
    public $tid;//线程id

    public function __construct($data)
    {
        $this->tid = $data['tid'];
    }

    public function run(){
        for ($i=0;$i<3;$i++){
            $body  = curl('https://www.baidu.com');
            $body = substr($body,0,5);
            echo date("Y-m-d H:i:s",time())."  id: ".$this->tid." num: $i state:$body \n";
            sleep(rand(1,3));
        }
    }
}

//执行线程
class PThead extends Thread
{

    public $clas;//
    public $data;
    public function __construct($clas, $data) {
        $this->clas = $clas;
        $this->data = $data;
    }

    public function run() {
        if (!class_exists($this->clas)){
            return false;
        }
        $run = new $this->clas($this->data);
        $run->run();
    }

}

//线程池
class Pools
{
    public $pool = array();//线程池
    public $clas;//任务类
    //public $data;//任务类 参数

    public function __construct($count, $clas) {
        $this->count = $count;
        $this->clas = $clas;
        //$this->data = $data;
    }
    public function push($data){
        if(count($this->pool) < $this->count){
            $this->pool[] = new PThead($this->clas, $data);
            return true;
        }else{
            return false;
        }
    }
    public function start(){
        foreach ( $this->pool as $id => $worker){
            $this->pool[$id]->start();
        }
    }
    public function join(){
        foreach ( $this->pool as $id => $worker){
            $this->pool[$id]->join();
        }
    }
    public function clean(){
        foreach ( $this->pool as $id => $worker){
            if(! $worker->isRunning()){
                unset($this->pool[$id]);
            }
        }
    }
}


//body
$pool = new Pools(100, 'doing');
for ($i=1;$i<20000;$i++){
    while (true){
        if($pool->push(['tid'=>$i])){
            break;
        }else{
            $pool->start();
            $pool->join();
            $pool->clean();
        }

    }
}
$pool->start();
$pool->join();
