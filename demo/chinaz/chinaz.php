<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/8/6
 * Time: 11:53
 */

class Chinaz
{
    private $db;
    private $client;
    public $thread = 10;
    public $host;
    public $list_test_result = array();

    public function __construct($db, $client)
    {
        $this->db = $db;
        $this->client = $client;
    }

    public function addSite($type, $url, $name){
        if(empty($type) || empty($url) || empty($name)){
            return false;
        }
        $data = [
            'type' => $type,
            'url' => $url,
            'name' => $name,
        ];

        $this->db->insert('17ce_url_list',$data);
        return true;
    }

    public function getSite(){
        $data = $this->db->get('17ce_url_list',1);
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
        if (empty($data)){
            return false;
        }else{
            return $data;
        }
    }

    public function delUrl($id){
        $this->db->where('id='.$id)->delete('17ce_url_list',1);
        return true;
    }

    public function getProvince($data){
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

    /**
     * use for:保存测试服务器列表
     * auth: Joql
     * @return array|bool
     * date:2018-08-06 14:05
     */
    public function saveTestPoint(){
        $url='http://tool.chinaz.com/speedtest/ping.pe';

        $response = $this->client->get($url, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
                'Pragma' => 'no-cache',
                'Referer' => 'http://tool.chinaz.com/'
            ],
        ]);
        if($response->getStatusCode() != 200) return false;
        $result = $response->getBody();

        preg_match('/enkey.*\"(.*)\"/', $result, $enkey);
        preg_match('/speedlist[\s\S]*/', $result, $listbody);
        preg_match_all('/id=\"(.*?)\"[\s\S]*?serveruroup.*?>(.*?)</', $listbody[0], $list);

        $data = array();
        foreach ($list[1] as $k=>$v){
            $has=$this->db->where('gid="'.$v.'"')->get('chinaz_hosts',1);
            if(empty($has)){
                $this->db->insert('chinaz_hosts',[
                    'gid' => $v,
                    'province' => $this->getProvince($list[2][$k]),
                    'detail' => $list[2][$k],
                    'enkey' => $enkey[1],

                ]);
            }
        }
        return true;
    }

    /**
     * use for:获取测试服务器列表
     * auth: Joql
     * date:2018-08-06 16:58
     */
    public function getTestPoint(){
        $this->host = $this->db->get('chinaz_hosts');
    }

    /**
     * use for:测试网站速度
     * auth: Joql
     * @param $data
     * @return array
     * date:2018-08-06 16:58
     */
    public function getTestPointResult($host= 'ping.pe'){
        $url='http://tool.chinaz.com/iframe.ashx?t=ping';
        $ishost = 1;
        $checktype = 1;
        //$host = $_POST['host'];

        //创建请求
        $requests = function () use ($url, $host){
            foreach ($this->host as $v){
                yield function () use ($v, $url, $host){
                    return $this->client->postAsync($url,[
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
                            'encode' => $v['enkey'],
                        ] ,
                    ]);
                };
            }
        };

        //创建线程池
        $pool = new \GuzzleHttp\Pool($this->client, $requests(),[
            'concurrency'=>$this->thread,
            'fulfilled'=>function($response, $index){
                if($response->getStatusCode() == '200'){
                    $result = $response->getBody();
                    $result = substr($result, 1, strlen($result)-2);
                    $result = preg_replace('/,headers.*\'/','',$result);
                    $result= $this->ext_json_decode($result, true);
                    $result['province'] = $this->host[$index]['province'];
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
        return $this->list_test_result;
    }

    public function ext_json_decode($str, $mode=false){
        $str = str_replace('\'','"',$str);
        if(preg_match('/\w:/', $str)){
            $str = preg_replace('/(\w+):/is', '"$1":', $str);
        }
        return json_decode($str, $mode);
    }


}