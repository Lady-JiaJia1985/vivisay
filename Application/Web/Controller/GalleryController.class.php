<?php
namespace Web\Controller;
use Think\Controller;
class GalleryController extends CommonController{

    protected $gallery_model;

    public function _initialize(){
        parent::_initialize();
        $this->gallery_model = D('Gallery');
    }

    public function index(){
        $this->display();
    }

    public function galleryList(){
        $category = I('post.cat');  //文章分类
        $curPage = I('post.curpage') ? I('post.curpage') : 1;
        $pageSize = (int)get_conf('GALLERY_INDEX_PAGESIZE'); //获取gallery首页分页大小配置

        //gallery list
        $data = $this->gallery_model->getGalleryList($curPage, $pageSize, 'post_time', 'desc', $category);

        if($data){
            success('获取数据成功', $data);
        }
        else{
            error('获取数据失败');
        }

    }

    public function detail($id){
        $resData = $this->gallery_model->getOneGallery($id);
        if(!$resData){
            $this->render404();
        }
        $this->assign('data', $resData);
        $this->assign('pagetitle', $resData['title']);
        $this->display();
    }

}