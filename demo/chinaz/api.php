<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/7/28
 * Time: 13:35
 */

require '../../init.php';

use GuzzleHttp\Client;



if(!empty($argv[1])){
    $act = $argv[1];
}
if(!empty($_GET['act'])){
    $act = $_GET['act'];
}

switch ($act){
    case 'getTestPoint':
        getTestPoint();
        break;
    case 'getTestPointResult':
        $api = new Api();
        $api->getTestPointResult($_POST);
        break;
    case 'addSite':
        addSite();
        break;
    case 'getSite':
        getSite();
        break;
    case 'delUrl':
        delUrl();
        break;
    default:
        returnAjax(0,'denined');
}


function getTestPoint(){
    $url='http://tool.chinaz.com/speedtest/ping.pe';

    $client = new Client();

    $response = $client->get($url, [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
            'Pragma' => 'no-cache',
            'Referer' => 'http://tool.chinaz.com/'
        ],
    ]);
    if($response->getStatusCode() != 200) returnAjax(0,'err');
    $result = $response->getBody();

    preg_match('/enkey.*\"(.*)\"/', $result, $enkey);
    preg_match('/speedlist[\s\S]*/', $result, $listbody);
    preg_match_all('/id=\"(.*?)\"[\s\S]*?serveruroup.*?>(.*?)</', $listbody[0], $list);

    $data = array();
    foreach ($list[1] as $k=>$v){
        $data['list'][] = [
            'gid' => $v,
            'province' => getProvince($list[2][$k]),
            'detail' => $list[2][$k],
        ];
    }
    $data['enkey'] = $enkey[1];

    returnAjax(1,'success',$data);
}


class Api
{
    public $thread = 10;
    public $list_test_result = array();

    public function getTestPointResult($data){
        $url='http://tool.chinaz.com/iframe.ashx?t=ping';
        $ishost = 1;
        $checktype = 1;
        //$host = $_POST['host'];
        $host = 'ping.pe';
        $guids = $data['guids'];
        $encode = $data['encode'];

        $client = new Client();
        //创建请求
        $requests = function () use ($client,$guids, $url, $host, $encode){
            foreach ($guids as $v){
                yield function () use ($client, $v, $url, $host, $encode){
                    return $client->postAsync($url,[
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)',
                            'Referer' => 'http://tool.chinaz.com/speedtest/'.$host
                        ],
                        'verify' => false,
                        'form_params' => [
                            'host' => $host,
                            'ishost' => 1,
                            'checktype' => 1,
                            'guid' => $v['gid'],
                            'encode' => $encode,
                        ] ,
                    ]);
                };
            }
        };

        //创建线程池
        $pool = new \GuzzleHttp\Pool($client, $requests(),[
            'concurrency'=>$this->thread,
            'fulfilled'=>function($response, $index) use ($guids){
                if($response->getStatusCode() == '200'){
                    $result = $response->getBody();
                    $result = substr($result, 1, strlen($result)-2);
                    $result = preg_replace('/,headers.*\'/','',$result);
                    $result=ext_json_decode($result, true);
                    $result['province'] = $guids[$index]['province'];
                    $this->list_test_result[] = $result;
                    //返回失败 再次请求
                }
            },
            'rejected' => function($reason, $index){
                echo $reason;
            }
        ]);
        $promise = $pool->promise();
        $promise->wait();
        returnAjax(1,'success',$this->list_test_result);
    }

}




function checkUser(){
    $url='https://www.17ce.com/sitex/checkuser';

    $p_url = $_POST['url'];
    $p_type = $_POST['type'];
    $p_isp = $_POST['isp'];
    $client = new Client();
    try{
        $response = $client->post($url,[
            'verify' => false,
            'headers' => [
                'Referer' => 'https://www.17ce.com/'
            ],
            'form_params' => [
                'isp' => $p_isp,
                'type' => $p_type,
                'url' => $p_url
            ]
        ]);
    }catch (Exception $e){
        try{
            $url='https://www.17ce.com/site/checkuser';
            $response = $client->post($url,[
                'verify' => false,
                'headers' => [
                    'Referer' => 'https://www.17ce.com/'
                ],
                'form_params' => [
                    'isp' => $p_isp,
                    'type' => $p_type,
                    'url' => $p_url
                ]
            ]);
        }catch (Exception $e1){
            returnAjax(0,'网络异常');
        }
    }

    if($response->getStatusCode() != 200) returnAjax(0,'err');
    $result = $response->getBody();
    returnAjax(1,'success',json_decode($result,true));
}

function addSite(){
    global $db;
    (empty($_POST['type']) || empty($_POST['url']) || empty($_POST['name'])) && returnAjax(0,'err');
    $data = [
        'type' => $_POST['type'],
        'url' => $_POST['url'],
        'name' => $_POST['name'],
    ];

    $db->insert('17ce_url_list',$data);
    returnAjax(1,'success');
}
function getSite(){
    global $db;

    $data = $db->get('17ce_url_list',1);
    foreach ($data as $k=>$v){
        switch ($v['type']){
            case '1':
                $data[$k]['prot'] = 'http';
                break;
            case '2':
                $data[$k]['prot'] = 'https';
                break;
        }
    }
    returnAjax(1,'success',$data);
}


function delUrl(){
    global $db;
    (empty($_POST['id'])) && returnAjax(0,'err');
    $filter = [
        'id' => $_POST['id']
    ];

    $db->where('id='.$_POST['id'])->delete('17ce_url_list',1);
    returnAjax(1,'success');
}

/**
 * use for:获取省份名称
 * auth: Joql
 * @param $data
 * @return mixed
 * date:2018-08-05 10:10
 */
function getProvince($data){
    $province = [
        "安徽",
        "浙江",
        "湖南",
        "江西",
        "福建",
        "广东",
        "香港",
        "广西",
        "云南",
        "西藏",
        "新疆",
        "贵州",
        "江苏",
        "上海",
        "山东",
        "河南",
        "河北",
        "天津",
        "北京",
        "辽宁",
        "吉林",
        "黑龙江",
        "四川",
        "陕西",
        "山西",
        "重庆",
        "湖北",
        "内蒙古",
        "宁夏",
        "青海",
        "甘肃",
        "台湾",
        "海南",
        "澳门",
        "广东",
        "广西",
        "台湾",
        "海南",
    ];
    foreach ($province as $v){
        if(strpos($data, $v) !== false){
            return $v;
        }
    }
}

