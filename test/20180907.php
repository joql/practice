<?php
require 'init.php';

use GuzzleHttp\Client;


$log = new \Monolog\Logger('诺云接口测试');
$log->pushHandler(new \Monolog\Handler\StreamHandler('./public/log/demo-ny-api.log',\Monolog\Logger::INFO));
$client = new Client();

$api = new Api('', $client, $log);
$api->test();

class Api
{
    private $db;
    private $client;
    private $log;
    public $thread = 300;
    public $total = 3000000;

    public $url = 'https://api.nuoyun.tv/app_detail/appstatus1?app=oh2ew2nl';

    public function __construct($db, $client, $log)
    {
        $this->db = $db;
        $this->client = $client;
        $this->log = $log;
    }

    public function test(){
        //创建请求
        $requests = function (){
            while ($this->total > 0){
                yield function (){
                    return $this->client->get($this->url,[
                        'verify' => false,
                    ]);
                };
                $this->total --;
            }
        };

        //创建线程池
        $pool = new \GuzzleHttp\Pool($this->client, $requests(),[
            'concurrency'=>$this->thread,
            'fulfilled'=>function($response, $index){
                if($response->getStatusCode() == '200'){
                    $result = $response->getBody();
                    $this->console($index.'---'.$result);
                    //$this->log->info($result);
                }
            },
            'rejected' => function($reason, $index){
                $this->console($index.'---'.$reason);
                //$this->log->info($reason);
            }
        ]);
        $promise = $pool->promise();
        $promise->wait();
        $this->console('结束');
    }
    private function console($data){
        echo date('Y-m-d H:i:s').": $data\n";
    }
}