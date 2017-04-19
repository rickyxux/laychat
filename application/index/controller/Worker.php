<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 17-3-24
 * Time: 下午3:47
 */

namespace app\index\controller;


use think\worker\Server;
use Workerman\Lib\Timer;

class Worker extends Server
{
    protected $socket = 'websocket://www.tp5.com:2346';
    protected $processes = 1;
    protected $uidConnections = array();

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {

        //判断当前客户端是否已经验证，即是否设置啦uid
        if (!isset($connection->uid)) {
            //没有验证的话把第一个包当作uid
            $connection->uid = $data;
            /**
             * 保存uid到connection的映射，这样科研方便的通过uid 查找connection
             * 实现对特定uid推送数据
             */
            $this->uidConnections[$connection->uid] = $connection;
            return ;
        }
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        echo "new connection from ip ".$connection->getRemoteIp()."\n";
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        global $this;
        if (isset($connection->uid)) {
            //连接断开是删除映射
            unset($this->uidConnections[$connection->uid]);
        }
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        /*
        $timer = new Timer();
        $timer->add(10, function ()use($worker){
            //遍历当前进程所有的客户的连接，发送但前服务器时间
            foreach($worker->connections as $connection) {
                $connection->send(time());
            }
        });
        */

        //开启一个内部断开，方便内部系统推送数据，Text协议格式，文本+换行符
        $inner_text_worder = new \Workerman\Worker('text://www.tp5.com:5678');
        $inner_text_worder->onMessage = function ($connection, $buffer)
        {
            echo 'hhh';
            //$data数组格式，里面有uid，表示向那个uid的页面推送数据
            $data = json_decode($buffer, true);
            $uid = $data['uid'];
            //通过workerman向uid的页面推送数据
            $ret = $this->sendMessageByUid($uid, $buffer);
            //返回推送结果
            $connection->send($ret? 'ok' : 'fail');
        };
        //执行监听
        $inner_text_worder->listen();


    }

    /**
     * 连接的应用层发送缓冲区数据全部发送完毕时触发
     *
     * @param $worker
     */
    public function onBufferFull($worker)
    {

    }

    /**
     *
     * @param $worker
     */
    public function onWorkerReload($worker)
    {
        foreach ($worker->connections as $connection) {
            $connection->send('worker reloading');
        }
    }

    //向所有验证的用户推送数据
    function broadcast($message) {
        foreach ($this->uidConnections as $connection) {
            $connection->send($message);
        }
    }

    //针对uid推送数据
    function sendMessageByUid($uid, $message) {
        if (isset($this->uidConnections[$uid])) {
            $connection = $this->uidConnections[$uid];
            $connection->send($message);
            return true;
        }
        return false;

    }


}