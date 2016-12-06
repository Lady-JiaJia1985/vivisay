<?php
namespace Web\Controller;

use Think\Controller;
use Vendor\WechatSDK\WechatApi;
use Vendor\Wechat\Wechat;
use Home\Addon\wxBizMsgCrypt\wxBizMsgCrypt;

class WechatController extends Controller {

    private $_wxConfig = null;  //微信配置appid appsecret...
    private $_weChatObj = null;  //微信sdk

    /*
     * 架构函数  获取配置
     * */
    public function __construct(){
        parent::__construct();
        $this->_wxConfig = C('weixin');
        $this->_weChatObj = new Wechat($this->_wxConfig);
    }

    //接入
    public function index() {
        if (!$_GET['echostr']) {
            $type = $this->_weChatObj->getRev()->getRevType();
            switch($type) {
                case Wechat::MSGTYPE_TEXT:
                    $this->wxTextReply();
                    break;
                case Wechat::MSGTYPE_EVENT:
                    $this->wxEventReply();
                    break;
                case Wechat::MSGTYPE_IMAGE:
                    break;
                default:
                    $this->_weChatObj->text("你发的消息我无法识别")->reply();
            }
        }else{
            $this->_weChatObj->valid();
        }
    }

    //微信文本回复消息
    private function wxTextReply(){
        $wechatObj = $this->_weChatObj;
        $keyword = $wechatObj->getRevContent();
        $userOpenid = $wechatObj->getRevFrom();

        if($keyword == '我的信息'){
            $userInfo = $wechatObj->getUserInfo($userOpenid);
            $infoStr = '昵称：'.$userInfo['nickname']."\n".
                        '性别：'.($userInfo['sex']!=0 ? ($userInfo['sex']==1?'男':'女') : '未知')."\n".
                        '所在城市：'.$userInfo['country'].' '.$userInfo['province'].' '.$userInfo['city']."\n";
            $wechatObj->text($infoStr)->reply();
        }
        else{
            $wechatObj->text(':( 我不明白你在说什么')->reply();
        }

    }

    //微信事件回复消息
    private function wxEventReply(){
        $wechatObj = $this->_weChatObj;
        $eve = $wechatObj->getRevEvent();
        $openid = $wechatObj->getRevFrom();
        if($eve['event'] == 'subscribe'){
            $wechatObj->text('欢迎关注薇薇说微信号')->reply();
        }
//        if($eve['event'] == 'CLICK' && $eve['key'] == 'kf'){
//            $wechatObj->transfer_customer_service()->reply();
//            $kfMsg = ["touser"=>"{$openid}","msgtype"=>"text","text"=>["content"=>"已为您接入在线客服"]];
//            $wechatObj->sendCustomMessage($kfMsg);
//        }
    }

    public function test(){
        $openid = wx_openid();
        dump($openid);
    }
}