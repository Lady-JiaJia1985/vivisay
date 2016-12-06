<?php
/**
 * 博客数据模型 数据表：vv_post_detail
 * Created by jarry chen.
 * Date: 2016/7/7
 * Time: 15:32
 */
namespace Admin\Model;
use Think\Model;
class PostEntryModel extends Model{

    protected $_validate = array(
        array('title','require','文章标题必须填写！'),
        array('author','require','作者必须填写！'),
        array('category','require','至少选择一个文章分类！'),
        array('content','require','文章内容必须填写！'),
    );

    protected $_auto = array (
        //array(完成字段,完成规则,[完成条件,附加规则]),
        array('post_time','get_cur_datetime',1,'function'),  // 对post_time字段在新增的时候写入当前时间戳
        array('update_time','get_cur_datetime',2,'function')  // 对update_time字段在更新的时候写入当前时间戳
    );


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
        $map['status'] = array('neq', -1);
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

    //删除文章
    public function deletePostById($postid){
        return $this->where(array('id'=>$postid))->delete();
    }

}
