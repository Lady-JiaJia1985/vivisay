<?php
/**
 * admin 模块通用方法
 */

//将用户信息放入session
function userToSession($user){
    $sessUser['id'] = $user['id'];
    $sessUser['user_type'] = $user['usertype'];
    $sessUser['user_name'] = $user['username'];
    $sessUser['nick_name'] = $user['nickname'];
    $sessUser['avatar'] = $user['avatar'];
    if($sessUser) session('user', $sessUser);
}

/* *
 * admin模块的LOG
 * @param   $label  标识
 * @param   $data   记录数据
 * */
function admin_log($label='LOG DATA:', $data){
    $path = '/Logs/Web/';
    $file = ROOT_PATH.$path.date('Ymd').'.log';
    $log = is_array($data) ? var_export($data, true) : $data;
    $log = '['.date('Y-m-d H:i:s').']'."\r\n".$label.$log."\r\n\r\n";
    file_put_contents($file, $log, FILE_APPEND);
}

/*
 * 文章状态转样式文字(样式基于bootstrap)
 * @param   int     $statuscode
 * return   string
 * */
function post_status_text($statuscode){
    switch ($statuscode){
        case 1:
            return '<span class="text-success">已发布</span>';break;
        case 0:
            return '<span class="text-muted">未发布</span>';break;
        case -1:
            return '<span class="text-danger">已删除</span>';break;
        default:
            return '<span class="text-warning">未知</span>';
    }
}

/*
 * 配置状态转样式文字(样式基于bootstrap)
 * @param   int     $statuscode
 * return   string
 * */
function config_status_text($statuscode){
    switch ($statuscode){
        case 1:
            return '<span class="text-success">已启用</span>';break;
        case 0:
            return '<span class="text-danger">已禁用</span>';break;
        default:
            return '<span class="text-warning">未知</span>';
    }
}