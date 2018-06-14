<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2018/6/13
 * Time: 14:57
 */

require '../../init.php';
require_once '../qiniu/auth.php';

use GuzzleHttp\Client;

//$db->insert()
$yueru = new YueRu(new \GuzzleHttp\Client(), $db);
$yueru->run();


class YueRu
{
    public $client;// guzzlehttp
    public $db;//mysqldb
    private $room_data;//房间信息
    private $friend_room_no;//其他房间号

    public function __construct($client, $db)
    {
        $this->client = $client;
        $this->db = $db;
    }

    public function run(){

        for ($i=847;$i<3000;$i++){
            unset($this->room_data);
            unset($this->friend_room_no);
            unset($result_pic);
            unset($result_user_in);
            unset($parm);
            $url = 'http://m.yueru.com/Send/Post/api/Room/GetRoomInfoByIRoomNo';
            $parm = [
                'iroomNo'=>'ZZ'.sprintf('%08d',$i).'A'
            ];

            if($this->getRoomData($url, $parm) !== false){
                //检测房间是否存在
                if(empty($this->room_data['ObjectData']['RoomSignText'])){
                    continue;
                }
                //保存房间信息
                $this->saveRoom() === false ? $this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO'].' 保存失败'):$this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO'].' 保存成功');
                //保存图片信息
                $result_pic = $this->savePic();
                $result_pic === false ? $this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO'].' 图片保存失败'):$this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO']." 成功保存".count($result_pic).'张图片');
                //保存入住信息
                $result_user_in = $this->saveFriendInfo();

                $result_user_in === false ? $this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['RoomNO'].' 用户入住信息保存失败'):$this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['RoomNO']." 成功保存".count($result_user_in).'位入住信息');
                //获取朋友房间
                $this->getFriendRoomNo();
                foreach ($this->friend_room_no as $v){
                    unset($result_pic);
                    unset($this->room_data);
                    unset($parm);
                    $parm = [
                        'iroomNo'=>$v
                    ];
                    if($this->getRoomData($url, $parm) !== false){
                        $this->saveRoom() === false ? $this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO'].' 保存失败'):$this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO'].' 保存成功');
                        //保存图片信息
                        $result_pic = $this->savePic();
                        $result_pic === false ? $this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO'].' 图片保存失败'):$this->console('房间 '.$this->room_data['ObjectData']['VRoomInfo']['IRoomNO']." 成功保存".count($result_pic).'张图片');
                    }else{
                        $this->console('房间 '.$v.' 信息获取失败');
                        continue;
                    }
                }
            }
        }

    }

    /**
     * use for:获取房间信息
     * auth: Joql
     * @param $url
     * @param $parm
     * @return bool
     * date:2018-06-13 15:11
     */
    private function getRoomData($url, $parm){
        unset($this->room_data);

        try{
            $response = $this->client->post($url,[
                'form_params'=>$parm
            ]);
        }catch (Exception $e){
            return false;
        }
        if($response->getStatusCode() != 200){
            return false;
        }
        $this->room_data = json_decode($response->getBody(), true);
        if(empty($this->room_data['ObjectData']['RoomSignText'])){
            return false;
        }
        return true;
    }

    /**
     * use for:保存房间记录
     * auth: Joql
     * @return bool
     * date:2018-06-13 15:41
     */
    private function saveRoom(){
        if(empty($this->room_data['ObjectData']['RoomSignText'])){
            return false;
        }
        $room_data = $this->room_data['ObjectData']['VRoomInfo'];

        $save = [
            'room_no'=> $room_data['IRoomNO'],
            'comm_name'=>$room_data['CommName'],
            'comm_address'=>$room_data['CommAddress'],
            'floor_num'=>$room_data['FloorNum'],
            'house_no'=>$room_data['HouseNO'],
            'to_ward'=>$room_data['Towards'],
            'admin_address'=>$room_data['AdminAddress'],
            'property_address'=>$room_data['PropertyAddress'],
            'unit_info'=>$room_data['UnitInfo'],
            'room_area'=>$room_data['RoomArea'],
            'price'=>$room_data['Price'],
            'room_address'=>$room_data['IRoomAddress'],
            'rental_state'=>($room_data['RentalStateInfo'] == '已出租')?2:1,
            'zone_name'=>$room_data['ZoneName'],
            'release_data'=>strtotime($room_data['ReleasedDate']),
            'floor_no'=>$room_data['FloorNO'],
            'room_num'=>$room_data['RoomNum'],
            'public_config'=>$room_data['PublicConfig'],
            'baisc_config'=>$room_data['BaiscConfig'],
            'lng'=>$room_data['Lng'],
            'lat'=>$room_data['Lat'],
            'city_id'=>$room_data['CityID'],
            'zone_id'=>$room_data['ZoneID']
        ];
        //var_dump($save);die();
        return $this->db->insert('yueru_room',$save);
    }

    /**
     * use for:获取朋友房间号
     * auth: Joql
     * @return bool
     * date:2018-06-13 15:51
     */
    private function getFriendRoomNo(){
        if(empty($this->room_data['ObjectData']['RoomSignText'])){
            return false;
        }
        $room_list = $this->room_data['ObjectData']['RoomNoList'];
        foreach ($room_list as $k=>$v){
            if(!empty($v['IRoomNO'])){
                $this->friend_room_no[] = $v['IRoomNO'];
            }
        }
        return true;
    }

    /**
     * use for:保存入住信息
     * auth: Joql
     * @return bool
     * date:2018-06-14 10:28
     */
    private function saveFriendInfo(){
        if(empty($this->room_data['ObjectData']['RoomSignText'])){
            return false;
        }
        $friend_data = $this->room_data['ObjectData']['RoomFriend'];
        $save = [];

        foreach ($friend_data as $v){
            //是否为空
            if(empty($v['CustNO'])){
                continue;
            }
            //用户信息是否存在
            $stat = $this->db->where("cust_no = '".$v['CustNO']."'")->has('yueru_room_user_in');
            if($stat === false){
                $save[] = [
                    'cust_no'=>$v['CustNO'],
                    'cust_name'=>$v['CustName'],
                    'sex'=>$v['Sex'],
                    'room_no'=>$v['IRoomNO'],
                    'room_area'=>$v['RoomArea'],
                    'price'=>$v['Price'],
                    'plan_check_in_date'=>strtotime($v['PlanCheckinDate']),
                    'profession'=>$v['Profession'],
                    'loves'=>$v['Loves']
                ];
                $this->console('用户 '.$v['CustName'].' 加入保存队列');
            }else{
                continue;
            }
        }
        //保存
        return $this->db->insertMulti('yueru_room_user_in',$save);
    }

    /**
     * use for:保存图片
     * auth: Joql
     * @return bool
     * date:2018-06-13 17:10
     */
    private function savePic(){
        if(empty($this->room_data['ObjectData']['RoomSignText'])){
            return false;
        }
        $save = [];
        //房间图片
        foreach ($this->room_data['ObjectData']['PicList'] as $picv){
            //检测图片是否存在并转换
            if($this->db->where('pic_id='.$picv['PicID'])->has('yueru_room_pic')){
                $this->console('图片 '.$picv['PicID'].' 已存在');
                continue;
            }else{
                $pic_url = $this->getQiNiuPicUrl('http://m.yueru.com'.$picv['PicUrl']);
                if($pic_url === false){
                    $this->console('图片 '.$picv['PicID'].' 转存七牛云失败');
                }else{
                    $save[] = [
                        'room_no' => $this->room_data['ObjectData']['VRoomInfo']['IRoomNO'],
                        'pic_id'=> $picv['PicID'],
                        'pic_url'=> $pic_url,
                        'addtime'=>strtotime($picv['UploadTime'])
                    ];
                    $this->console('图片 '.$picv['PicID'].' 加入队列成功');
                }
            }
        }
        //公共区域图片
        foreach ($this->room_data['ObjectData']['PublicList'] as $ppicv){
            //检测图片是否存在并转换
            if($this->db->where('pic_id='.$ppicv['PicID'])->has('yueru_room_pic')){
                $this->console('图片 '.$ppicv['PicID'].' 已存在');
                continue;
            }else{
                $pic_url = $this->getQiNiuPicUrl('http://m.yueru.com'.$ppicv['PicUrl']);
                if($pic_url === false){
                    $this->console('图片 '.$ppicv['PicID'].' 转存七牛云失败');
                }else{
                    $save[] = [
                        'room_no' => $this->room_data['ObjectData']['VRoomInfo']['IRoomNO'],
                        'pic_id'=> $ppicv['PicID'],
                        'pic_url'=> $pic_url,
                        'addtime'=>strtotime($ppicv['UploadTime'])
                    ];
                    $this->console('图片 '.$ppicv['PicID'].' 加入队列成功');
                }
            }
        }
        //小区图片
        foreach ($this->room_data['ObjectData']['LpPicList'] as $lpicv){
            //检测图片是否存在并转换
            if($this->db->where('pic_id='.$lpicv['PicID'])->has('yueru_room_pic')){
                $this->console('图片 '.$lpicv['PicID'].' 已存在');
                continue;
            }else{
                $pic_url = $this->getQiNiuPicUrl('http://m.yueru.com'.$lpicv['PicUrl']);
                if($pic_url === false){
                    $this->console('图片 '.$lpicv['PicID'].' 转存七牛云失败');
                }else{
                    $save[] = [
                        'room_no' => $this->room_data['ObjectData']['VRoomInfo']['IRoomNO'],
                        'pic_id'=> $lpicv['PicID'],
                        'pic_url'=> $pic_url,
                        'addtime'=>strtotime($lpicv['UploadTime'])
                    ];
                    $this->console('图片 '.$lpicv['PicID'].' 加入队列成功');
                }
            }
        }

        //保存
        return $this->db->insertMulti('yueru_room_pic',$save);
    }

    /**
     * use for: 获取七牛图片地址
     * auth: Joql
     * @param $pic_url
     * @return bool|string
     * date:2018-06-13 16:39
     */
    private function getQiNiuPicUrl($pic_url){
        $accessKey = 'VbzfGpZyNJMXEMvKeetBVixVqB8pipP0IbjetEJG';
        $secretKey = 'KMdZfb956ZQl0ndkHoWbe3YxUNPNZcmADEUFOizM';

        $qiniu = new QiNiu($accessKey, $secretKey);
        $data = $qiniu->fetch('picture',$pic_url);
        try{
            $response = $this->client->get("http://iovip-as0.qbox.me".$data[0],[
                'headers'=>[
                    'Authorization' =>"QBox ".$data[1]
                ]
            ]);
        }catch (Exception $e){
            return false;
        }
        if($response->getStatusCode() != 200){
            return false;
        }
        $img = json_decode($response->getBody(), true);
        if(empty($img['key'])){
            return false;
        }else{
            return 'http://paaldiona.sabkt.gdipper.com/'.$img['key'];
        }
    }


    private function console($data){
        echo date('Y-m-d H:i:s').": $data\n";
    }
}