<?php
/**
 * Admin Gallery controller
 * Date: 2016/11/22
 * Time: 09:55
 */
namespace Admin\Controller;
use Think\Controller;

class GalleryController extends CommonController {

    public function _initialize(){
        parent::_initialize();
        $this->gallery_model = D('Gallery');
    }

    //图文列表首页
    public function galleryList(){
        $this->display('list');
    }

    //图文分页列表 return json
    public function getGalleryList(){
        $page       = I('post.page') ? I('post.page') : 1;
        $pagesize   = I('post.pagesize') ? I('post.pagesize') : 5;
        $ordername  = I('post.sortname') ? I('post.sortname') :'id';
        $ordertype  = I('post.sortorder') ? I('post.sortorder') :'asc';
        $category   = I('post.category') ? I('post.category') :'';
        $searchkey  = I('post.searchkey') ? I('post.searchkey') :'';

        $data = $this->gallery_model->getGalleryList($page, $pagesize, $ordername, $ordertype, $category, $searchkey);

        if($data){
            foreach($data['list'] as $k=>$v){
                $data['list'][$k]['statusText'] = post_status_text($v['status']);
            }
            success('获取数据成功', $data);
        }
        else
            error('无数据');
    }

    //add a gallery
    public function add(){
        $cats = M('PostCategory')->select();
        $this->assign('cats', $cats);
        $this->display();
    }

    //edit gallery
    public function edit(){
        $galleryid = I('get.id');

        $gallery = $this->gallery_model->where(array('id'=>$galleryid))->find();
        $gallery['category'] = str_to_array($gallery['category']);
        $gallery['src'] = json_decode($gallery['src'], true);

        //图文分类
        $cats = M('PostCategory')->select();

        $this->assign('cats', $cats);
        $this->assign('gallery', $gallery);
        $this->display('edit');
    }

    public function doPost(){
        $post = I('post.');

        isset($post['category']) && $post['category'] = array_to_str($post['category']);

        isset($post['src']) && $post['src'] = json_encode($post['src']);

        //发布、保存 和 编辑
        switch ($post['act']){
            case 'edit':
                if (!$this->gallery_model->create($post)){
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    error($this->gallery_model->getError());
                }else{
                    // 验证通过 可以进行其他数据操作
                    if($this->gallery_model->save()){
                        success('编辑成功');
                    }
                    else{
                        error('编辑失败');
                    }
                }
                break;
            case 'publish':
                $post['status'] = 1;  //发布时状态为1

                if (!$this->gallery_model->create($post)){
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    error($this->gallery_model->getError());
                }else{
                    // 验证通过 可以进行其他数据操作
                    if(isset($post['id']) && $post['id']>0){
                        //有id 表示是已有的图文发布
                        if($this->gallery_model->save()){
                            success('发布成功');
                        }
                        else{
                            error('发布失败');
                        }
                    }else{
                        if($this->gallery_model->add()){
                            success('发布成功');
                        }
                        else{
                            error('发布失败');
                        }
                    }
                }
                break;
            case 'save':
                $post['status'] = 0;  //未发布时状态为0

                if (!$this->gallery_model->create($post)){
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    error($this->gallery_model->getError());
                }else{
                    // 验证通过 可以进行其他数据操作
                    if($this->gallery_model->add()){
                        success('保存成功');
                    }
                    else{
                        error('保存失败');
                    }
                }
                break;
            default:
                error('参数错误');
                break;
        }

    }

    //删除一篇图文
    public function deleteGallery(){
        $galleryid = I('post.id');

        if(isset($galleryid) && $galleryid>0){
            if($this->gallery_model->deleteGalleryById($galleryid)){
                success('图文已删除');
            }
            else{
                error('删除失败');
            }
        }else{
            error('参数错误');
        }
    }

}