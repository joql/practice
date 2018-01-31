<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/31
 * Time: 9:58
 */

//error_reporting(0);
//开启php_sockets 扩展

//set_time_limit(0);


class Scan
{
    private $host;
    private $port;

    public function setPosrt($host,$port){
        if(empty($host) || empty($port)){
            return false;
        }
        $this->host = $host;
        $this->port = $port;
        return true;
    }
    public function checkPost(){
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($sock);
        socket_connect($sock,$this->host,$this->port);
        socket_set_block($sock);
        $r = array($sock);
        $w=array($sock);
        $f= array($sock);
        switch (socket_select($r, $w, $f,5)){
            case 0:
                return ['code'=>0,'msg'=>'超时'];
                break;
            case 1:
                return ['code'=>1,'msg'=>'打开'];
                break;
            case 2:
                return ['code'=>0,'msg'=>'关闭'];
                break;
            default:
                return ['code'=>0,'msg'=>'未知错误'];
        }
    }
}