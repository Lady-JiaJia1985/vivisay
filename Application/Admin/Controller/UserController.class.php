<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends CommonController{

    public function loginpage(){
        $this->display();
    }

    //用户登录
    public function doLogin(){
        $post = I('post.');
//        dump($post);exit;

        $auto_login = (isset($post['autologin']) && $post['autologin']=='Y') ? true : false;

        $user_model = D('User');

        if($user_model->userLogin($post['username'], $post['password'], $auto_login)){
            $this->success('登录成功', U('index/index'), 2);
        }
        else{
            $this->error($user_model->getError(), U('user/loginpage'), 3);
        }
    }

    //用户退出登录
    public function doLogout(){
        $user_model = D('User');
        if($user_model->userLogout()){
            $this->success('用户已退出登录', U('user/loginpage'), 2);
        }
        else{
            //todo 退出失败时逻辑
            $this->error('退出失败');
        }
    }

}