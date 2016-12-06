<?php
/**
 * kindeditor上传图片到七牛
 * Created by PhpStorm.
 * User: luotuo
 * Date: 2015/6/3
 * Time: 17:31
 */
date_default_timezone_set ( 'Asia/Shanghai' );
$config = array(
    'QINIU_AK' => 'RzYvCfc2uWRmKmEcO1EMxnB_AfXMbHO702Yw3OKK',
    'QINIU_SK' => 'epuMXbZjMUvE7cJsUy4VDo2SsHM-0tmPzybWxJCG',
    'QINIU_HOST' => 'http://o8g3j83l6.bkt.clouddn.com',         //前面一定要加http://
    'QINIU_BUCKET' => 'vivisay',
    'QINIU_RSF_HOST' => 'http://rsf.qbox.me',
    'SITE_URL' => 'http://www.vivisay.com/Public/kindeditor/misc/'   //用于七牛回调
);

function token($data, $accessKey, $secretKey){
    $data = str_replace(array('+', '/'), array('-', '_'), base64_encode(json_encode($data)));
    $sign = hash_hmac('sha1', $data, $secretKey, true);
    return $accessKey.':'.str_replace(array('+', '/'), array('-', '_'), base64_encode($sign)).':'.$data ;
}
//分应用目录标识，文件管理时根据此参数划分目录
//$prefix = $_GET['prefix']?trim($_GET['prefix']):'vivisay';
$prefix = $config['QINIU_BUCKET'];
$act = $_GET['act'];

switch($act){
    case 'getToken':
        $type = $_GET['type'];

        if (!in_array($type, array(1, 2, 3))){
            exit('access deny');
        }

        header("Content-type:text/html;charset=utf-8");
        $bucket = $config['QINIU_BUCKET'];
        $host = $config['QINIU_HOST'];
        $accessKey = $config['QINIU_AK'];
        $secretKey = $config['QINIU_SK'];
        $fileName = $prefix.'/'.date('Ymd').'/'.rand(1000, 9999).'$(fname)';

        $data =  array(
            "scope" => $bucket,
            "saveKey" => $fileName,
            "deadline" => time() + 3600
        );
        //token1用于单文件上传调用，由于Kindeditor单文件上传是通过提交给隐藏的Iframe实现，因此要通过303重定向来返回值，因此要配置returnUrl和returnBody
        $data1 = array_merge($data, array('returnUrl' => $config['SITE_URL'].'keQiniu.php?act=uploadReturn', 'returnBody' => '{"url": "'.$host.'/'.$fileName.'", "size": $(fsize), "name": "$(fname)"}'));
        $token1 = token($data1, $accessKey, $secretKey);
        //token2用于多文件上传时，回调服务器进行相关数据处理，比如记录上传文件的信息，如果不需要记录，也可以不设置callbackUrl和callbackBody
        $data2 = array_merge($data, array('callbackUrl' => $config['SITE_URL'].'keQiniu.php?act=uploadCallback', 'callbackBody' => 'url='.$host.'/'.$fileName.'&size=$(fsize)&name=$(fname)'));
        $token2 = token($data2, $accessKey, $secretKey);

        header('Content-Type:application/json; charset=utf-8');
        if($type == 3){
            exit(json_encode(array('token1' => $token1, 'token2' => $token2)));
        }else if($type == 2){
            exit(json_encode($token2));
        }else{
            exit(json_encode($token1));
        }
        break;
    case 'fileManage':
        $_GET['path'] = rtrim($_GET['path'], '/');
        //七牛请求参数整理
        $path = empty($_GET['path']) ? $prefix : $prefix.'/'.$_GET['path'];
        $url = '/list?'.http_build_query(array('bucket' => $config['QINIU_BUCKET'], 'delimiter' => '/', 'prefix' => $path.'/'));

        $sign = hash_hmac('sha1', $url."\n", $config['QINIU_SK'], true);
        $token = $config['QINIU_AK'].':'.str_replace(array('+', '/'), array('-', '_'), base64_encode($sign));

        $header = array('Host: rsf.qbox.me', 'Content-Type:application/x-www-form-urlencoded', 'Authorization: QBox '.$token);

        //七牛请求获取数据
        $curl = curl_init ();
        curl_setopt($curl, CURLOPT_URL, trim($config['QINIU_RSF_HOST'].$url,'\n'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "");
        $qiniuResult = json_decode(curl_exec($curl), true);
        curl_close($curl);

        //按照Kindeditor格式组合数据
        $file_list = array();
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
        foreach ($qiniuResult['items'] as $k => $v){
            $file_ext = strtolower(pathinfo($v['key'], PATHINFO_EXTENSION));
            $file_list[] = array(
                'is_dir' => false,
                'has_file' => false,
                'filesize' => $v['fsize'],
                'is_photo' => in_array($file_ext, $ext_arr),
                'filename' => str_ireplace($path.'/', '', $v['key']),
                'datetime' => date('Y-m-d H:i:s', (int)($v['putTime']/10000000))
            );
        }
        if(isset($qiniuResult['commonPrefixes'])){
            foreach ($qiniuResult['commonPrefixes'] as $k => $v){
                $name = explode('/', $v);
                $file_list[] = array(
                    'is_dir' => true,
                    'has_file' => true,
                    'filename' => $name[1]
                );
            }
        }
        $result['moveup_dir_path'] = '';
        $result['current_dir_path'] = $_GET['path'];
        $result['current_url'] = $config['QINIU_HOST'].'/'.$path.'/';
        $result['file_list'] = $file_list;

        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
        break;
    case 'uploadReturn':
        /*
        这里可以直接通过$_GET['upload_ret']获取自定义的返回数据，数据是经过编码的，解码后可得到json格式字符串，解码方式看下方代码
        获取后可以进行存库等操作
        最后一步返回的数据是按照Kindeditor的格式返回
        */
        if (empty($_GET['upload_ret'])){
            exit('access deny');
        }else{
            $str = json_decode(base64_decode(str_replace(array('-', '_'), array('+', '/'), $_GET['upload_ret'])), true);
            exit('{"error":0, "url": "'.$str['url'].'"}');
        }
        break;
    case 'uploadCallback':
        /*
        这里可以直接通过$_POST获取自定义的返回数据，比如图片名称，地址，大小等
        获取后可以进行存库等操作
        最后一步返回的数据，七牛云会原样返回给客户端，这里也就是按照Kindeditor的格式返回
        */
        exit('{"error":0, "url": "'.$_POST['url'].'"}');
        break;
}