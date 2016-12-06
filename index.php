<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
// define('APP_DEBUG', true);
//环境定义
define('ENV', 'prod');

//  前台URL
define ( 'BASE_URL', 'http://www.vivisay.com' );
//  系统根目录
define ( 'ROOT_PATH', str_replace('/index.php', '', str_replace('\\', '/', __FILE__)) );
// 定义应用目录
define('APP_PATH','./Application/');
// 绑定Home模块到当前入口文件
//define('BIND_MODULE','Home');
//网站名称
define('SITE_NAME', 'VIVISAY');
//模板路径
define('TMPL_PATH','./Template/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
