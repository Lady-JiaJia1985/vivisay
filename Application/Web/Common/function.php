<?php
/**
 * web common functions
 * by jarry
 **/

/* *
 * WEB模块的LOG
 * @param   $label  标识
 * @param   $data   记录数据
 * */
function web_log($label='LOG DATA:', $data){
    $path = '/Logs/Web/';
    $file = ROOT_PATH.$path.date('Ymd').'.log';
    $log = is_array($data) ? var_export($data, true) : $data;
    $log = '['.date('Y-m-d H:i:s').']'."\r\n".$label.$log."\r\n\r\n";
    file_put_contents($file, $log, FILE_APPEND);
}
