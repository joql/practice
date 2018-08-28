<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/8/27
 * Time: 16:01
 */
require '../../../init.php';


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

//$db->insert()
$log = new \Monolog\Logger('天眼查');
$log->pushHandler(new \Monolog\Handler\StreamHandler('../../../public/log/demo-tianyian-api.log',\Monolog\Logger::INFO));


$api = new Api(new Client(), $db, $log);
switch ($act = $_GET['act'] ? $_GET['act'] : $argv[1]){
    case 'test':
        $api->getMoreCompany('北京+上海+郑州+深圳+武汉', '传媒', 100);
        break;
    case 'get_tianyan_detail':
        $api->getMorePage('传媒', 30);
        break;
    case 'get_tianyan_detail_db':
        $data = $db->where('search_key=\'传媒\'')->get('zhilian_company');
        foreach ($data as $v){
            $api->getMorePage($v['company_name'], 1);
        }
        break;
    case 'get_pic_url':
        $api->getPicUrl();
        break;
}

class Api
{
    public $client;
    public $db;
    public $log;
    public $token;
    public function __construct($client, $db, $log)
    {
        $this->client = $client;
        $this->db = $db;
        $this->log = $log;
        $this->token = [
            'auth_token' => 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIxNzE4MzgxMzI0MSIsImlhdCI6MTUzNTQzOTk2MSwiZXhwIjoxNTUwOTkxOTYxfQ.4Us3kYuoryH8re1u61hf6DUeOprp2o3KDUhfxWIAYM_Z5mAk3slYh28LFnC3wGqKN8tg-xI4FhQwF9nDHMUwrg'
        ];
    }
    public function getDetail($company_id){
        $url = 'https://www.tianyancha.com/company/'.$company_id;

        $cookieJar = CookieJar::fromArray($this->token, 'www.tianyancha.com');  // 此处记得请求域名需要保持跟请求的url host一致，否则不会携带此cookie。
        try{
            $response = $this->client->get($url, [
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                    'Referer' => 'https://www.tianyancha.com/',
                ],
                'cookies'     => $cookieJar
            ]);
        }catch (Exception $e){
            return false;
        };
        if($response->getStatusCode() != 200){
            return false;
        }
        preg_match_all('/电话.*?>([\d|-]+?)<[\s\S]*?title=\"(.*?)\"[\s\S]*stopPropagation\(event\)\">(.*?)<.*?他有[\s\S]*?title=\"(.*?)\"/', $response->getBody(), $result);
        return [
            'tel' => $result[1][0],
            'email' => $result[2][0],
            'name' => $result[3][0],
            'company_name' => $result[4][0],
        ];
    }
    public function getPageInfo($key, $page){

        $url = 'https://www.tianyancha.com/search/p'.$page.'?key='.urlencode($key);

        $cookieJar = CookieJar::fromArray($this->token, 'www.tianyancha.com');  // 此处记得请求域名需要保持跟请求的url host一致，否则不会携带此cookie。
        try{
            $response = $this->client->get($url, [
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                    'Referer' => 'https://www.tianyancha.com/',
                ],
                'cookies'     => $cookieJar
            ]);
        }catch (Exception $e){
            return false;
        };
        if($response->getStatusCode() != 200){
            return false;
        }
        preg_match_all('/left-item[\s\S]*?alt=\"(.*?)\"[\s\S]*?title=\"(.*?)\"[\s\S]*?title=\"(.*?)\"[\s\S]*?title=\"(.*?)\"[\s\S]*?link-hover-click\">(.*?)</', $response->getBody(), $result);
        $data = [];
        foreach ($result[1] as $k=>$v){
            $data[] = [
                'company_name' => strip_tags($result[1][$k]),
                'legal_name' => $result[2][$k],
                'regist_capital' => $result[3][$k],
                'regist_time' => strtotime($result[4][$k]),
                'legal_tel' => $result[5][$k],
                'search_key' => $key,
                'search_page' => $page,
            ];
        }
        return $data;
    }

    public function getMorePage($key, $page=5){

        for ($i=1; $i<=$page;$i++){
            $data = $this->getPageInfo($key, $i);
            if(empty($data)){
                $this->log->info('关键词：'.$key.', 页码：'.$i.', 保存失败');
            }else{
                $this->db->insertMulti('tianyan', $data);
                $this->log->info('关键词：'.$key.', 页码：'.$i.', 保存完毕');
            }
            sleep(rand(30,120));
        }
    }

    public function getCompanyName($area, $key, $page){
        $url = 'https://sou.zhaopin.com/jobs/searchresult.ashx?jl='.urlencode($area).'&kw='.urlencode($key).'&p='.$page.'&kt=2&isadv=0';

        $cookieJar = CookieJar::fromArray([
            'ZP_OLD_FLAG' => 'true'
        ], 'sou.zhaopin.com');  // 此处记得请求域名需要保持跟请求的url host一致，否则不会携带此cookie。
        try{
            $response = $this->client->get($url, [
                'verify' => false,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
                    'Referer' => 'https://sou.zhaopin.com/',
                ],
                'cookies'     => $cookieJar
            ]);
        }catch (Exception $e){
            return false;
        };
        if($response->getStatusCode() != 200){
            return false;
        }
        preg_match_all('/gsmc.*?_blank\">(.*?)<\/a/', $response->getBody(), $result);
        $data = [];
        foreach ($result[1] as $k=>$v){
            $data[] = [
                'company_name' => strip_tags($result[1][$k]),
                'search_key' => $key,
                'search_area' => $area,
            ];
        }
        return $data;
    }


    public function getMoreCompany($area, $key, $page=30){

        for ($i=1; $i<=$page;$i++){
            $data = $this->getCompanyName($area, $key, $i);
            if(empty($data)){
                $this->log->info('智联,关键词：'.$key.', 页码：'.$i.', 保存失败');
            }else{
                $this->db->insertMulti('zhilian_company', $data);
                $this->log->info('智联,关键词：'.$key.', 页码：'.$i.', 保存完毕');
            }
            sleep(1);
        }
    }
    private function console($data){
        echo date('Y-m-d H:i:s').": $data\n";
    }
};
