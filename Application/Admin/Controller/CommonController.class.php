<?php
/* +----------------------------
 * 通用控制器
 *
 * */
namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller {

    //不需要登陆的action数组
    protected static $_noLogginActs = array(
        'user/loginpage',
        'user/dologin'
    );

    public function _initialize() {
        $this->assign('cur_path', strtolower(CONTROLLER_NAME.'/'.ACTION_NAME));  //当前的controller/action (exp: blog/edit)
        //判断用户是否登录
        //未登录跳转登录页
        if(!$this->loginCheck()){
            $this->error('用户未登录！', U('user/loginpage'), 4);
        }
        else{
            $menu = $this->renderMenu();
            $this->assign('user', session('user'));
            $this->assign('menu', $menu);
        }
        trace(session());  //todo 线上去掉
    }

    //登录检测
    protected function loginCheck(){
        $act = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
        //跳转登录页条件：1.action不在 不需要登录的列表里
        //             2.未登录 (session中没有用户信息；cookie未设置自动登陆)
        if (!in_array($act, self::$_noLogginActs) && !$this->isUserLogged()){
            return false;
        }
        else{
            return true;
        }

    }

    /* Has this user logged
     * 1.session中是否有登录信息
     * 2.cookie中是否设置了自动登录
     * return boolean
     * */
    protected function isUserLogged(){
        $userId = session('user.id');
        //session has userid
        if(!$userId) {
            //cookie has userid
            if(cookie('auth')){
                $salt = C('SALT');
                list($identifier, $token) = explode(':', cookie('auth'));
                $user = M('User')->where(array('identifier'=>$identifier))->find();
                if($user){
                    if($user['token'] != $token) return false;
                    elseif($user['identifier']!=md5($salt . md5($user['id'] . $salt))) return false;
                    else{
                        userToSession($user);
                        return true;
                    }
                }
                else return false;
            }
            else return false;
        }
        else return true;
    }

    //获取导航菜单(缓存优先)
    protected function renderMenu(){
        if($cache = read_from_cache('vivi_admin_menu')){
            return $cache;
        }

        $items = M('Menu')->where(array('pid'=>0))->order(array('pid'=>'asc'))->select();
        foreach ($items as $k=>$v) {
            $child = M('Menu')->where(array('pid'=>$v['id']))->order(array('id'=>'asc'))->select();
            $items[$k]['child'] = $child;
        }
        S('vivi_admin_menu', $items);
        return $items;
    }

}