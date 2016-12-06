<?php
/* +----------------------------
 * web通用控制器
 *
 * */
namespace Web\Controller;

use Think\Controller;

class CommonController extends Controller {

    public function _initialize(){
        $category = $this->getBlogCat();
        $this->assign('category', $category);
        $this->assign('pagetitle', SITE_NAME);
    }

    //获取导航菜单
    protected function getBlogCat(){
        $cats = M('PostCategory')->field(array('id', 'cat_id', 'cat_name'))->select();
        return $cats;
    }

    //404页面
    protected function render404(){
        $this->display('Public:404');
        exit;
    }


}