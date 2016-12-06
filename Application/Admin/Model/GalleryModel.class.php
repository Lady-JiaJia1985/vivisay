<?php
/**
 * Gallery Model -> vv_gallery
 * Date: 2016/11/9
 * Time: 15:06
 */
namespace Admin\Model;
use Think\Model;
class GalleryModel extends Model{

    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        array('title','require','文章标题必须填写！'),
        array('category','require','至少选择一个文章分类！'),
        array('src','require','至少上传一张图片')
    );

    protected $_auto = array (
        //array(完成字段,完成规则,[完成条件,附加规则]),
        array('post_time','get_cur_datetime',1,'function'),  // 对post_time字段在新增的时候写入当前时间戳
        array('update_time','get_cur_datetime',2,'function')  // 对update_time字段在更新的时候写入当前时间戳
    );

    /*
     * 获取gallery列表
     * @param $curPage      当前页数
     * @param $pageSize     每页个数
     * @param $orderName    排序名称
     * @param $orderType    排序方式 id | post_time | comments
     * @param $category     按分类
     * @param $searchKey    模糊查询key
     * @return $mixed
     * */
    public function getGalleryList($curPage = 1, $pageSize = 5, $orderName = 'id', $orderType = 'asc', $category = '', $searchKey = ''){
        if($category != '') $map['_string'] = 'find_in_set('.$category.', category)';  //查询category是否含有此分类
        if($searchKey != '') $map['title'] = array('like', '%'.$searchKey.'%');

        $list = $this->where($map)->order("{$orderName} {$orderType}")->limit($pageSize)->page($curPage)->select();

        if(!$list){
            return null;
        }

        foreach($list as $k=>$v){
            $list[$k]['src'] = json_decode($v['src'], true);
        }

        $count = $this->where($map)->count();

        $data['list'] = $list;
        $data['curPage'] = $curPage;
        $data['pageSize'] = $pageSize;
        $data['pageNum'] = ceil($count/$pageSize);
        $data['total'] = $count;
        return $data;
    }

    //删除图文
    public function deleteGalleryById($galleryid){
        return $this->where(array('id'=>$galleryid))->delete();
    }

}