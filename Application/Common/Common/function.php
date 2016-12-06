<?php
//通用脚本

//当前时间 格式：yyyy-mm-dd hh:mm:ss
function get_cur_datetime(){
    return date('Y-m-d H:i:s', time());
}

/*
 * 字符串转数组
 * 默认 “,” 分割
 * */
function str_to_array($str, $sep = ','){
    if(!is_string($str)) return $str;
    return explode($sep, $str);
}

/*
 * 数组转字符串
 * 默认 “,” 分割
 */
function array_to_str($arr, $sep = ','){
    if(!is_array($arr)) return $arr;
    return implode($sep, $arr);
}

//从HTML代码中提取IMG
function get_img_tags($html){
    if($html=='') return $html;
    //匹配正则
    $preg = '/<img.*?src=[\"|\'](.*?)[\"|\'].*?>/i';
    preg_match_all($preg, $html, $imgArr);
    return $imgArr;
}

//生成图片缩略图
/*IMAGE_THUMB_SCALE     =   1 ; //等比例缩放类型
  IMAGE_THUMB_FILLED    =   2 ; //缩放后填充类型
  IMAGE_THUMB_CENTER    =   3 ; //居中裁剪类型
  IMAGE_THUMB_NORTHWEST =   4 ; //左上角裁剪类型
  IMAGE_THUMB_SOUTHEAST =   5 ; //右下角裁剪类型
  IMAGE_THUMB_FIXED     =   6 ; //固定尺寸缩放类型
*/
function img_to_thumb($img, $width=150, $height=150){
    $imgUrl = ltrim($img, '/');  //去掉最左侧的‘/’，否则路径会出错
    //把文件名称和类型分开
    $arr = explode('.', $imgUrl);
    $name = $arr[0];
    $type = $arr[1];
    $imgObj = new \Think\Image();
    try{
        $imgObj->open($imgUrl);
        //生成缩略图文件 如：原图_thumb150x150.jpg
        $imgObj->thumb($width, $height, \Think\Image::IMAGE_THUMB_SCALE)->save($name.'_thumb'.$width.'x'.$height.'.'.$type);
    }catch(Think\Exception $e){
        return '';
    }
    //缩略图文件地址
    $thumbImgUrl = '/'.$name.'_thumb'.$width.'x'.$height.'.'.$type;
    return $thumbImgUrl;
}

//获取配置
function get_conf($confName){
    $conf = M('Config')->where(array('conf_name'=>$confName))->field('conf_value')->find();
    return $conf['conf_value'];
}

/**
 * 获取随机字符串
 * @param number $length
 * @return string
 */
function random_str($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for($i = 0; $i < $length; $i ++) {
        $str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
    }
    return $str;
}

// 获取当前用户的OpenId
function get_wx_openid() {
    $openid = session('openid');
    //如果session中没有拿到openid，就到微信授权接口中获取
    if ( empty($openid) ) {
        $callback = get_cur_url();
        wx_oauth($callback);
    }
    return (string) $openid;
}

// 获取微信基础access_token，自动带缓存功能
function get_wx_access_token() {
    $wechat = new \Vendor\Wechat\Wechat(C('weixin'));
    return $wechat->checkAuth();
}

/**
 * 获取微信授权
 * @param   $callback   //回调url
 * @param   $scope      //snsapi_base       (静默授权不能获取用户信息)
 *                      //snsapi_userinfo   (用户需授权获取用户信息)
 */
function wx_oauth($callback, $scope='snsapi_base') {
    $wechat = new \Vendor\Wechat\Wechat(C('weixin'));
    if (!isset($_GET['getOpenId'])) {
        //回调URL有参数的话+&  否则+?
        if(strpos($callback, '?') !== false) $callback .= '&getOpenId=1';
        else $callback .= '?getOpenId=1';

        $state = random_str(10);
        redirect($wechat->getOauthRedirect($callback, $state, $scope));
    } elseif ($_GET['code']) {
        $json = $wechat->getOauthAccessToken();
        session('openid', $json['openid']);
        session('oauth', $json);
        //为了防止刷新重定向后的网页造成无限请求，所以将？后的code,state参数从url中去掉
        $callback = cut_wxauth_url_params($callback);

        redirect($callback);
    }
}


//获取微信用户信息
function get_wx_userinfo($openid, $refresh=false){
    if( session('wx_userinfo') && $refresh==false ) return session('wx_userinfo');
    $wechat = new \Vendor\Wechat\Wechat(C('weixin'));
    $userinfo = $wechat->getUserInfo($openid);
    if( $userinfo['subscribe']==0 ){
        $oauth = session('oauth');
        $userinfo = $wechat->getOauthUserinfo($oauth['access_token'], $openid);
        //如果近期未授权过
        if(!$userinfo){
            //用户授权获取用户信息
            wx_oauth(get_cur_url(),'snsapi_userinfo');
            get_wx_userinfo($openid);
        }
    }
    !isset($userinfo['subscribe']) && $userinfo['subscribe'] = 0;
    session('wx_userinfo', $userinfo);
    return $userinfo;
}

function access_token_is_valid($access_token, $openid){
    $wechat = new \Vendor\Wechat\Wechat(C('weixin'));
    $isValid = $wechat->getOauthAuth($access_token, $openid);
    return $isValid;
}

//此脚本为了去掉微信授权后URL上带的参数 如&getOpenId=1&code=021I4IiH1cZYtd00YAfH1NPDiH1I4Iir&state=WY5rdiQ2ao
function cut_wxauth_url_params($url){
    if(false !== strpos($url, '?getOpenId=1&code=')){
        $temp_arr = explode('?getOpenId=1&code=', $url);
    }elseif(false !== strpos($url, '&getOpenId=1&code=')){
        $temp_arr = explode('&getOpenId=1&code=', $url);
    }else{
        return $url;
    }
    return $temp_arr[0];
}

/**
 * GET 请求
 * @param string $url
 */
function http_get($url) {
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * POST 请求
 * @param string $url
 */
function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach($param as $key=>$val){
            $aPOST[] = $key."=".urlencode($val);
        }
        $strPOST =  join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

function success($msg = '操作成功！', $data = array()){
    header('Content-Type:application/json; charset=utf-8');
    $response['error'] = 0;
    $response['msg'] = $msg;
    $response['data'] = $data;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

function error($msg = '操作失败！', $data = array()){
    header('Content-Type:application/json; charset=utf-8');
    $response['error'] = 1;
    $response['msg'] = $msg;
    $response['data'] = $data;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// php获取当前访问的完整url地址
function get_cur_url() {
    $url = 'http://';
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        $url = 'https://';
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        $url = 'https://';
    }
    $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

//读取缓存
function read_from_cache($cacheName){
    if($cacheName == '') return '';
    return S($cacheName);
}