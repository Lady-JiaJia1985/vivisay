<?php
return array(
	//'配置项'=>'配置值'
    // 开启路由
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
        'blog/edit/:id\d$' => array('blog/edit', '', array('method'=>'get')),
        'gallery/edit/:id\d$' => array('gallery/edit', '', array('method'=>'get')),
    ),

    //视图模板路径(相当于根目录)
    'VIEW_PATH'=>'./Template/Admin/'
);