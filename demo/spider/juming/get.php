<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/23
 * Time: 14:18
 */

require '../../../init.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;


error_reporting(E_ALL & ~E_NOTICE);
global $db;

$juming = new juming($db);
$juming->run();

class juming{
    private $client;
    private $db;
    private $cookie;
    private $theads=2;//线程数
    private $url_list;//域名列表


    public function __construct($db)
    {
        $this->client = new Client();
        $this->db = $db;
    }

    public function run(){
        //登陆
        //$this->login();
        //获取域名列表
        //$this->getUrlId();
        //微信封禁检测
        $this->getUrlList();
        //$this->checkWxState();
        $this->getDetailInfo();
    }

    public function login(){


        $response = $this->client->get('http://www.juming.com/index.htm',[
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'
            ]
        ]);

        if($response->getStatusCode() != 200 ) return false;

        $cookies = $response->getHeaders();

        foreach ($cookies['Set-Cookie'] as $k=>$v){
            if(strpos($v,'ASPSESSION') !== false){
                $this->cookie = explode('=',current(explode(';',$v)));
                break;
            }
        }

        echo 'cookie: '.$this->cookie[0].'='.$this->cookie[1]."\n";
        $body = mb_convert_encoding($response->getBody(), 'utf-8', 'gbk');
        preg_match("/flogin2\(this,\'(.*)\'/",$body,$code);
        echo 'code: '.$code[1]."\n";
        $cookieJar = CookieJar::fromArray([
            $this->cookie[0] => $this->cookie[1]
        ], 'www.juming.com');
        $login = $this->client->post('http://www.juming.com/if.htm',[
            'form_params'=>[
                'tj_fs'=>'1',
                're_yx'=>'672487663@qq.com',
                're_code'=>$code[1],
                're_mm'=>$this->getPwd('13037647351YIN',$code[1])
            ],
            'cookies'=>$cookieJar,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36'
            ]
        ]);
        //echo $login->getBody();
    }

    /**
     * use for:
     * auth: Joql
     * date:2018-06-16 22:21
     */
    public function getUrlId(){
        $list= array();
        for($i=1;$i<=90;$i++){
            $url = 'http://www.juming.com/ykj/?api_sou=1&sfba=1999&ymlx=0&qian2=100&jgpx=0&meiye=&page='.$i.'&_='.time().'176';
            $cookieJar = CookieJar::fromArray([
                $this->cookie[0] => $this->cookie[1]
            ], 'www.juming.com');  // 此处记得请求域名需要保持跟请求的url host一致，否则不会携带此cookie。
            try{
                $response = $this->client->get($url, [
                    'headers' => [
                        'User-Agent' => 'testing/1.0',
                    ],
                    'cookies'     => $cookieJar
                ]);
            }catch (Exception $e){
                continue;
            };
            if($response->getStatusCode() != 200) continue;
            preg_match_all('/value=\"(\d{7})\".*?target=\"_blank\">(.*)?<\/a>[\s\S]*?<td>(\d{2,3})元/',mb_convert_encoding($response->getBody(), 'utf-8', 'gbk'),$tmp);
            foreach ($tmp[1] as $k=>$v){
                $list[] = ['url_id'=>$tmp[1][$k],'url'=>html_entity_decode($tmp[2][$k]),'price'=>$tmp[3][$k]];
            }
            echo "get page $i  sucesss num:".count($tmp[1])."\n";
        }

        $this->db->insertMulti('juming_url_id_list',$list);

    }

    public function getUrlList(){
        $this->url_list = $this->db->get('juming_url_id_list');
    }

    public function checkWxState(){

        $client = $this->client;
        //创建请求
        $requests = function () use ($client){
            foreach ($this->url_list as $v){
                yield function () use ($client, $v){
                    $url = 'http://www.juming.com/mai_yes.htm?id='.$v['url_id'].'&_='.time().'897&wxjc=y';
                    return $client->getAsync($url,[
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)'
                        ]
                    ]);
                };
            }
        };

        //创建线程池
        $pool = new \GuzzleHttp\Pool($client, $requests(),[
            'concurrency'=>$this->theads,
            'fulfilled'=>function($response, $index){
                if($response->getStatusCode() == '200'){
                    $body = $response->getBody()->getContents();
                    $body = mb_convert_encoding($body, 'utf-8', 'gbk');
                    if(mb_strpos($body,'未拦截') !== false){
                        $this->db->where('id='.$this->url_list[$index]['id'])->update('juming_url_id_list',['wx_state'=>1],1);
                        echo 'body: '.$body.' url: '.$this->url_list[$index]['url'].'  ok'."\n";
                    }else{
                        $this->db->where('id='.$this->url_list[$index]['id'])->update('juming_url_id_list',['wx_state'=>0],1);
                        echo 'body: '.$body.' url: '.$this->url_list[$index]['url'].'  fail'."\n";
                    }
                }else{
                    echo 'body: code!=200 url: '.$this->url_list[$index]['url'].'  fail'."\n";
                }

            },
            'rejected' => function($reason, $index){
                echo 'body: code!=200 url: '.$this->url_list[$index]['url'].'  fail'."\n";
            }
        ]);
        $promise = $pool->promise();
        $promise->wait();
        echo "checked over \n";
    }

    public function getDetailInfo(){
        $client = $this->client;
        //创建请求
        $requests = function () use ($client){
            foreach ($this->url_list as $v){
                yield function () use ($client, $v){
                    $url = 'http://www.juming.com/mai_yes.htm?ym='.$v['url'];
                    return $client->getAsync($url,[
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)'
                        ]
                    ]);
                };
            }
        };

        //创建线程池
        $pool = new \GuzzleHttp\Pool($client, $requests(),[
            'concurrency'=>20,
            'fulfilled'=>function($response, $index){
                if($response->getStatusCode() == '200'){
                    $body = $response->getBody()->getContents();
                    $body = mb_convert_encoding($body, 'utf-8', 'gbk');
                    preg_match_all("/域名注册时间[\s\S]*?<td>(.*?)<[\s\S]*域名到期时间[\s\S]*?<td>(.*?)<[\s\S]*域名实际注册商[\s\S]*?<td>(.*?)</",$body,$match);

                    $this->db->where('id='.$this->url_list[$index]['id'])->update('juming_url_id_list',[
                        'regist_time'=>$match[1][0],
                        'expire_time'=>$match[2][0],
                        'register'=>$match[3][0]
                    ],1);
                    echo 'url: '.$this->url_list[$index]['url'].' detail ok'."\n";
                }else{
                    echo 'body: code!=200 url: '.$this->url_list[$index]['url'].' detail fail'."\n";
                }

            },
            'rejected' => function($reason, $index){
                echo 'body: code!=200 url: '.$this->url_list[$index]['url'].' detail fail'."\n";
            }
        ]);
        $promise = $pool->promise();
        $promise->wait();
        echo "get detail over \n";
    }

    private function getPwd($pwd, $code){
        return substr(md5($code.substr(md5('[jiami'.$pwd.'mima]'),0,19)),0,19);
    }
}



