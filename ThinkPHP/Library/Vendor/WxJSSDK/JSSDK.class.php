<?php
namespace Vendor\WxJSSDK;
/**
 * 微信JSSDK类
 * Created by Cangshu.
 * Date: 2016/6/7
 * Time: 11:10
 */
class JSSDK {

    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到缓存做示例
        $key = 'wechat_jsapi_ticket_'.$this->appId;
        $jsapi_ticket = S($key);
        if ($jsapi_ticket !== false) return $jsapi_ticket;

        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode(httpGet($url), true);
        $jsapi_ticket = $res['ticket'];
        if ($jsapi_ticket) {
            $expire = intval($res['expires_in']) - 200;
            S($key, $jsapi_ticket, $expire);
            return $jsapi_ticket;
        }else{
            return '';
        }
    }

    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到缓存做示例
        $key = 'wechat_access_token_'.$this->appId;
        $access_token = S($key);
        if ($access_token !== false) return $access_token;

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode(httpGet($url), true);
        $access_token = $res['access_token'];
        if ($access_token) {
            $expire = intval($res['expires_in']) - 200;
            S($key, $access_token, $expire);
            return $access_token;
        }else{
            return '';
        }
    }

    //暂时不用 有问题
//    private function httpGet($url) {
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
//        curl_setopt($curl, CURLOPT_URL, $url);
//
//        $res = curl_exec($curl);
//        curl_close($curl);
//
//        return $res;
//    }

}