<?php
/**
 * 文章分类 和 菜单 控制器
 * Date: 2016/11/28
 * Time: 10:48
 */
namespace Admin\Controller;
use Think\Controller;

class SystemController extends CommonController {

    //+++++++++++++++++++++++++++++++++++++++++++++++|
    //+++++++++++++ MENU CONFIG +++++++++++++++++++++|
    //+++++++++++++++++++++++++++++++++++++++++++++++|

    public function menuConfig(){
        $menuconfig = M('Menu')->where(array('pid'=>0))->order(array('pid'=>'asc'))->select();
        foreach ($menuconfig as $k=>$v) {
            $child = M('Menu')->where(array('pid'=>$v['id']))->order(array('id'=>'asc'))->select();
            $menuconfig[$k]['child'] = $child;
        }
        $this->assign('menuconfig', $menuconfig);  //这里assign的参数不能用menu,防止和commoncontroller中的menu冲突
        $this->display();
    }

    public function editMenu(){
        $post = I('post.');
//        dump($post);exit;

        //二级菜单需要验证链接
        if(isset($post['link'])){
            if(!preg_match('/[a-zA-Z]+\/[a-zA-Z]+[0-9]*/i', $post['link'])) error('链接格式有误!');
            $post['link'] = strtolower($post['link']);
        }

        //有id是编辑
        if($post['type']=='add'){
            if(M('menu')->add($post)){
                success('添加成功!');
            }else{
                error('添加失败!');
            }
        }
        elseif($post['type']=='edit'){
            if(M('menu')->where(array('id'=>$post['id']))->save($post)){
                success('编辑成功!');
            }else{
                error('编辑失败!');
            }
        }
        else{
            error('参数错误');
        }
    }

    //删除菜单
    public function deleteMenu(){
        $menuid = I('post.menuid');
        if(isset($menuid) && $menuid>0){
            if(M('Menu')->where(array('id'=>$menuid))->delete()){
                success('删除成功!');
            }else{
                error('删除失败!');
            }
        }else{
            error('参数错误');
        }
    }

    public function rebuildMenu(){
        $nowTime = I('post.time');
        if($nowTime){
            S('vivi_admin_menu', null);
            success('生成菜单成功!');
        }else{
            error('生成菜单失败!');
        }
    }


    //+++++++++++++++++++++++++++++++++++++++++++++++|
    //+++++++++++++ WEB MODULE CONFIG +++++++++++++++|
    //+++++++++++++++++++++++++++++++++++++++++++++++|

    public function moduleConfig(){
        $this->display();
    }

    public function moduleList(){
        $post = I('post.');

        $webModule = M('WebModule');
        $list = $webModule->order("{$post['sortname']} {$post['sortorder']}")->limit($post['pagesize'])->page($post['page'])->select();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['statusText'] = config_status_text($v['status']);
            }
        }
        $data['list'] = $list;
        $count = $webModule->count();
        $data['curPage'] = $post['page'];
        $data['pageSize'] = $post['pagesize'];
        $data['pageNum'] = ceil($count/$post['pagesize']);
        $data['total'] = $count;
        success('获取数据成功', $data);
    }

    public function edit(){
        $this->display('moduleedit');
    }

    //模块启用/禁用
    public function toggleStatus(){
        $moduleId = I('post.moduleid');
        if(isset($moduleId) && $moduleId>0){
            $cur_status = M('WebModule')->where(array('id'=>$moduleId))->field('status')->find();
            $now_status = $cur_status['status']==1 ? 0 : 1;
            if(M('WebModule')->where(array('id'=>$moduleId))->save(array('status'=>$now_status))){
                success('操作成功');
            }
            else{
                error('操作失败');
            }
        }else{
            error('参数错误');
        }
    }
}