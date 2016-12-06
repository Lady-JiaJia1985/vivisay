<?php
/**
 * 博客数据模型 数据表：vv_post_detail
 * Created by jarry chen.
 * Date: 2016/7/7
 * Time: 15:32
 */
namespace Web\Model;
use Think\Model;
class PostEntryModel extends Model{

    //获取一篇博客
    public function getOneBlog($postId){
        $postData = $this->where(array('id'=>$postId))->find();
        if($postData){
            $postData['content'] = htmlspecialchars_decode($postData['content']);
            //文章分类 ---
            $map['cat_id']  = array('in',$postData['category']);
            $catArr = M('PostCategory')->where($map)->field('cat_name')->select();
            $postData['category'] = $catArr;
        }
        //文章分类 end---
        return $postData;
    }

    //获取前一篇博客(时间 > curPostTime的最近一篇)
    public function getPreBlog($curPostTime){
        $map_pre['status'] = 1;
        $map_pre['post_time'] = array('gt', $curPostTime);
        $prePost = $this->where($map_pre)->order('post_time asc')->find();
        if($prePost) return $prePost;
        else return null;
    }

    //获取后一篇博客(时间 < curPostTime的最近一篇)
    public function getNxtBlog($curPostTime){
        $map_nxt['status'] = 1;
        $map_nxt['post_time'] = array('lt', $curPostTime);
        $nxtPost = $this->where($map_nxt)->order('post_time desc')->find();
        if($nxtPost) return $nxtPost;
        else return null;
    }

    /*
     * 获取博客文章列表
     * @param $curPage      页数
     * @param $pageSize     每页个数
     * @param $orderName    排序名称
     * @param $orderType    排序方式 id | post_time | comments
     * @param $category     按分类
     * @param $searchKey    模糊查询key
     * @return $mixed
     * */
    public function getPostList($curPage = 1, $pageSize = 5, $orderName = 'id', $orderType = 'asc', $category = '', $searchKey = ''){
        $map['status'] = 1;
        if($category != '') $map['_string'] = 'find_in_set('.$category.', category)';  //查询category是否含有此分类
        if($searchKey != '') $map['title'] = array('like', '%'.$searchKey.'%');

        $list = $this->where($map)->order("{$orderName} {$orderType}")->limit($pageSize)->page($curPage)->select();

        if(!$list){
            return null;
        }

        $data['list'] = $list;
        $count = $this->where($map)->count();
        $data['curPage'] = $curPage;
        $data['pageSize'] = $pageSize;
        $data['pageNum'] = ceil($count/$pageSize);
        $data['total'] = $count;
        return $data;
    }

    //文章阅读数量加1
    public function incrPostView($postid){
        return $this->where(array('id'=>$postid))->setInc('views');
    }


}
