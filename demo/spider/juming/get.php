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
$act = $argv[1];

$juming->login();
switch ($act){
    case 'get':
        $juming->getUrlId();
        break;
    case 'check':
        $juming->checkWxState();
}



class juming{
    private $client;
    private $db;
    private $cookie;


    public function __construct($db)
    {
        $this->client = new Client();
        $this->db = $db;
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
    public function getUrlId(){
        $list= array();
        for($i=10;$i<=90;$i++){
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

    public function checkWxState(){
        $list = $this->db->get('juming_url_id_list');
        foreach ($list as $v){
            $url = 'http://www.juming.com/mai_yes.htm?id='.$v['url_id'].'&_='.time().'897&wxjc=y';
            try{
                $response = $this->client->get($url, [
                    'headers' => [
                        'User-Agent' => 'testing/1.0'
                    ]
                ]);
            }catch (Exception $e){
                continue;
            }
            if($response->getStatusCode() != 200) continue;
            $body = $response->getBody()->getContents();
            $body = mb_convert_encoding($body, 'utf-8', 'gbk');
            if(mb_strpos($body,'未拦截') !== false){
                $this->db->where('id='.$v['id'])->update('juming_url_id_list',['wx_state'=>1],1);
                echo 'body: '.$body.' url: '.$v['url'].'  ok'."\n";
            }else{
                $this->db->where('id='.$v['id'])->update('juming_url_id_list',['wx_state'=>0],1);
                echo 'body: '.$body.' url: '.$v['url'].'  fail'."\n";
            }
        }
    }

    private function getPwd($pwd, $code){
        return substr(md5($code.substr(md5('[jiami'.$pwd.'mima]'),0,19)),0,19);
    }
}



