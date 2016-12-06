<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class BlogController extends CommonController {

    public function _initialize(){
        parent::_initialize();
        $this->blog_model = D('PostEntry');
    }

    //博客列表首页
    public function blogList(){
        $this->display('list');
    }

    //博客分页列表 return json
    public function getBlogList(){
        $page       = I('post.page') ? I('post.page') : 1;
        $pagesize   = I('post.pagesize') ? I('post.pagesize') : 5;
        $ordername  = I('post.sortname') ? I('post.sortname') :'id';
        $ordertype  = I('post.sortorder') ? I('post.sortorder') :'asc';
        $category   = I('post.category') ? I('post.category') :'';
        $searchkey  = I('post.searchkey') ? I('post.searchkey') :'';

        $data = $this->blog_model->getPostList($page, $pagesize, $ordername, $ordertype, $category, $searchkey);

        if($data) {
            foreach ($data['list'] as $k => $v) {
                $data['list'][$k]['statusText'] = post_status_text($v['status']);
            }
            success('获取数据成功', $data);
        }
        else
            error('无数据');
    }

    public function create(){
        $cats = M('PostCategory')->select();
        $this->assign('cats', $cats);
        $this->display();
    }

    public function edit(){
        $postid = I('get.id');

        $post = $this->blog_model->where(array('id'=>$postid))->find();
        $post['category'] = str_to_array($post['category']);
        //文章分类
        $cats = M('PostCategory')->select();

        $this->assign('cats', $cats);
        $this->assign('post', $post);
        $this->display('edit');
    }

    public function doPost(){
        $post = I('post.');
//        dump($post);exit;

        isset($post['category']) && $post['category'] = array_to_str($post['category']);

        //如果没有缩略图就从文章内容中取第一张图作为缩略图，内容也没图就留空
        if(!$post['thumb_img']){
            $imgArr = get_img_tags(htmlspecialchars_decode($post['content']));
            $post['thumb_img'] = $imgArr[1][0] ? $imgArr[1][0] : '';
        }
        //将获取到的图生成150*150的缩略图(图片传本地时需要)
        //$post['thumb_img'] = img_to_thumb($post['thumb_img']);

        //发布、保存 和 编辑
        switch ($post['act']){
            case 'edit':
                if (!$this->blog_model->create($post)){
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    error($this->blog_model->getError());
                }else{
                    // 验证通过 可以进行其他数据操作
                    if($this->blog_model->save()){
                        success('编辑成功');
                    }
                    else{
                        error('编辑失败');
                    }
                }
                break;
            case 'publish':
                $post['status'] = 1;  //发布时状态为1

                if (!$this->blog_model->create($post)){
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    error($this->blog_model->getError());
                }else{
                    // 验证通过 可以进行其他数据操作
                    if(isset($post['id']) && $post['id']>0){
                        //有id 表示是已有的文章发布
                        if($this->blog_model->save()){
                            success('发布成功');
                        }
                        else{
                            error('发布失败');
                        }
                    }else{
                        if($this->blog_model->add()){
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

                if (!$this->blog_model->create($post)){
                    // 如果创建失败 表示验证没有通过 输出错误提示信息
                    error($this->blog_model->getError());
                }else{
                    // 验证通过 可以进行其他数据操作
                    if($this->blog_model->add()){
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

    //删除一篇文章
    public function deletePost(){
        $postid = I('post.postid');

        if(isset($postid) && $postid>0){
            if($this->blog_model->deletePostById($postid)){
                success('文章已删除');
            }
            else{
                error('删除失败');
            }
        }else{
            error('参数错误');
        }

    }


}