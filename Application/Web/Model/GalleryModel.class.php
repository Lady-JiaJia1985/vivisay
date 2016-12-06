<?php
/**
 * Gallery Model -> vv_gallery
 * Date: 2016/11/9
 * Time: 15:06
 */
namespace Web\Model;
use Think\Model;
class GalleryModel extends Model{

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
        $map['status'] = 1;
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

    //获取一篇图文详情
    public function getOneGallery($galId){
        $galData = $this->where(array('id'=>$galId))->find();
        if($galData){
            //文章分类 ---
            $map['cat_id']  = array('in',$galData['category']);
            $catArr = M('PostCategory')->where($map)->field('cat_name')->select();
            $galData['category'] = $catArr;
            $galData['src'] = json_decode($galData['src'], true);
            //文章分类 end---
        }
        return $galData;
    }

}