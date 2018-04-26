<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/3/23
 * Time: 14:18
 */

require '../../../init.php';

use GuzzleHttp\Client;

error_reporting(E_ALL);
global $db;

$juming = new juming($db);
$act = $argv[1];

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


    public function __construct($db)
    {
        $this->client = new Client();
        $this->db = $db;
    }

    public function getUrlId(){
        $list= array();
        for($i=1;$i<=90;$i++){
            $url = 'http://www.juming.com/ykj/?api_sou=1&sfba=1999&ymlx=0&qian2=100&jgpx=0&meiye=&page='.$i.'&_='.time().'176';
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'testing/1.0'
                ]
            ]);
            if($response->getStatusCode() != 200) continue;
			echo "get page $i \n";
            preg_match_all('/value=\"(\d{7})\".*?target=\"_blank\">(.*)?<\/a>[\s\S]*?<td>(\d{2,3})元/',mb_convert_encoding($response->getBody(), 'utf-8', 'gbk'),$tmp);
            foreach ($tmp[1] as $k=>$v){
                $list[] = ['url_id'=>$tmp[1][$k],'url'=>$tmp[2][$k],'price'=>$tmp[3][$k]];
            }
        }

        $this->db->insertMulti('juming_url_id_list',$list);
    }

    public function checkWxState(){
        $list = $this->db->get('juming_url_id_list');
        foreach ($list as $v){
            $url = 'http://www.juming.com/mai_yes.htm?id='.$v['url_id'].'&_='.time().'897&wxjc=y';
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'testing/1.0'
                ]
            ]);
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
}



