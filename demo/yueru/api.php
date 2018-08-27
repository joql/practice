<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/8/27
 * Time: 16:01
 */
require '../../init.php';
require_once '../qiniu/auth.php';

use GuzzleHttp\Client;

//$db->insert()
$log = new \Monolog\Logger('yueru');
$log->pushHandler(new \Monolog\Handler\StreamHandler('../../public/log/demo-yueru-api.log',\Monolog\Logger::INFO));


$api = new Api(new \GuzzleHttp\Client(), $db, $log);
switch ($act = $_GET['act']){
    case 'list':
        $api->getList();
        break;
    case 'get_pic_url':
        $api->getPicUrl();
        break;
}

class Api
{
    public function __construct($client, $db, $log)
    {
        $this->client = $client;
        $this->db = $db;
        $this->log = $log;
    }
    public function getList(){
        $page = $_GET['page'];
        $limit = $_GET['limit'];

        $_GET['key']['comm_name']   && $this->db->where('comm_name', '%'.$_GET['key']['comm_name'].'%','like');

        $list = $this->db->withTotalCount()->orderBy('rental_state', 'asc')->orderBy('price', 'asc')->get('yueru_room',[($page-1)*$limit,$limit]);
        foreach ($list as $k=>$v){
            $list[$k]['rental_state'] = $this->changeRoomStat($v['rental_state']);
            $list[$k]['release_data'] = date('Y-m-d',$v['release_data']);
        }
        returnLayAjax(0, 'success', $list, $this->db->totalCount);
    }

    public function getPicUrl(){
        $room_no = $_GET['room_no'];
        $list = $this->db->where('room_no=\''.$room_no.'\'')->get('yueru_room_pic');
        returnAjax(1,'success',$list);
    }

    function changeRoomStat($stat=1){
        switch ($stat){
            case '1':
                return '未出租';
            case '2':
                return '已出租';

        }
    }
};
