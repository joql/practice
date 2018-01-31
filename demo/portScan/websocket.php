<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/1/30
 * Time: 10:38
 */

//由于swoole多进程，用户变量信息不能共享，使用$server->connections 获取所有客户端句柄

require_once 'scan.php';

$Imsock = new Imsock();



class Imsock
{
    private $_user_list = array();
    private $_server = null;

    public function __construct($ip_address='192.168.19.128', $ip_port=9502)
    {
        echo "swoole_websocket 服务开启\n";
        $this->_server = new swoole_websocket_server($ip_address, $ip_port);

        $this->_server->on('open', function($server, $request)
        {
            $this->_sopen( $server, $request );
            echo "用户 {$request->fd} 连接服务器\n";
        });

        $this->_server->on('message', function($server, $frame)
        {
            //$this->_sendmsg( $server, $frame );
            $this->checkPort($server,$frame);
        });

        $this->_server->on('close', function($server, $fd)
        {
            $this->_sclose( $server, $fd );
        });

        $this->_server->start();
    }

    private function _sopen($server, $request)
    {
        $this->_user_list[ $request->fd ] = 0;
    }

    // 给客户端发送信息
    private function _sendmsg(swoole_websocket_server $server, $frame)
    {
        $msg = "from {$frame->fd}: {$frame->data}\n";
        foreach ($server->connections as $k){
            $server->push($k,$msg);
            echo "用户 {$k} 推送成功\n";
        }
    }

    private function _sclose( swoole_websocket_server $server, $fd )
    {
        unset( $this->_user_list[$fd] );
        echo "用户 $fd  退出登录\n";
    }
    //检测ip端口
    private function checkPort(swoole_websocket_server $server, $frame){
        $hosts = explode(':',$frame->data);
        $scan = new Scan();
        $scan->setPosrt($hosts[0],$hosts[1]);
        $result = $scan->checkPost();
        $server->push($frame->fd,$result);
        echo "用户 ".$frame->fd." 推送".implode(',',$result)." 成功";
        return true;
    }
}


