<?php
/**
 * 博客评论数据模型 数据表：vv_post_comment
 * Created by jarry chen.
 * Date: 2016/7/7
 * Time: 15:32
 */
namespace Web\Model;
use Think\Model;
class PostCommentModel extends Model{

    /*
     * 获取博客文章的评论列表
     * @param $postId 文章id
     * @param $curPage 页数
     * @param $pageSize  每页个数
     * @param $orderName 排序名称
     * @param $orderType 排序方式
     * @return $mixed
     * */
    public function getCommentList($postId, $curPage = 1, $pageSize = 5, $orderName = 'post_time', $orderType = 'asc'){
        $list = $this->where(array('post_id'=>$postId))->order("{$orderName} {$orderType}")->limit($pageSize)->page($curPage)->select();
        return $list;
    }
}