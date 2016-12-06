<?php

return array(
	//'配置项'=>'配置值'
    // 开启路由
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
        //路由表达式 =>  array('路由地址','传入参数','限制条件')
        'blog/:id\d$' => array('blog/detail', '', array('method'=>'get')),
        'gallery/:id\d$' => array('gallery/detail', '', array('method'=>'get'))
    ),

    //视图模板路径(相当于根目录)
    'VIEW_PATH'=>'./Template/Web/'
);