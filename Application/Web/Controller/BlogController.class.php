<?php
namespace Web\Controller;
use Think\Controller;
use Think\Hook;

class BlogController extends CommonController{

    protected $blog_model;

    public function _initialize(){
        parent::_initialize();
        $this->blog_model = D('PostEntry');
    }

    //博客首页
    public function index(){
        $category = I('get.cat');  //文章分类
        $curPage = I('get.curpage') ? I('get.curpage') : 1;
        $pageSize = (int)get_conf('BLOG_INDEX_PAGESIZE'); //获取博客首页分页大小配置

        //文章列表
        $data = $this->blog_model->getPostList($curPage, $pageSize, 'post_time', 'desc', $category);
        $data['category'] = $category;

        //页数超出时显示错误
        if($curPage > $data['pageNum']){
            $this->render404();
        }

        $this->assign('data', $data);
        $this->display();
    }

    //文章详情
    public function detail(){
        $postId = I('get.id');

        $commentModel = D('PostComment');

        //文章阅读数+1
        $this->blog_model->incrPostView($postId);

        $postData = $this->blog_model->getOneBlog($postId);
        if(!$postData){
            $this->render404();
        }

        $prePostId = '';
        $nxtPostId = '';
        $thisPostTime = $postData['post_time'];  //此文的post_time

        //上一篇(按时间排)
        $prePost = $this->blog_model->getPreBlog($thisPostTime);
        if($prePost['id']){
            $prePostId = $prePost['id'];
        }
        //下一篇(按时间排)
        $nxtPost = $this->blog_model->getNxtBlog($thisPostTime);
        if($nxtPost['id']){
            $nxtPostId = $nxtPost['id'];
        }

        //获取评论列表
        $commentList = $commentModel->getCommentList($postId, 1, 5, 'post_time', 'desc');

        //推荐文章
        $recommendPosts = $this->blog_model->getPostList(1, 4, 'comments', 'desc');
        //去掉当前文章
        foreach($recommendPosts as $k=>$v){
            if($v['id'] == $postId) unset($recommendPosts[$k]);
        }

        $this->assign('post', $postData);
        $this->assign('comment', $commentList);
        $this->assign('recommendpost', $recommendPosts['list']);
        $this->assign('prepostid', $prePostId);
        $this->assign('nxtpostid', $nxtPostId);
        $this->assign('pagetitle', $postData['title']);
        $this->display('post');
    }
}