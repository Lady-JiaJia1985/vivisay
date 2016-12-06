<?php
/**
 * 后台用户数据模型 数据表：vv_user
 * Created by jarry chen.
 * Date: 2016/9/30
 */
namespace Admin\Model;

use Think\Model;

class UserModel extends Model{

    /*用户登录
     * @param string username
     * @param string password
     * @param boolean autologin     是否7天免登录
     */
    public function userLogin($username, $password, $autologin = false){
        $user = $this->where(array('username'=>$username))->find();
        if($user){
            $userPass = $this->userPassEncrypt($password);
            if($userPass==$user['password']){
                userToSession($user);
                if($autologin === true){
                    $this->setAutoLogin($user['id']);
                }
                return true;
            }
            else{
                $this->error = '密码错误';
                return false;
            }
        }
        else{
            $this->error = '未找到此用户';
            return false;
        }
    }

    //用户退出登录
    public function userLogout(){
        session('user', null);
        if(cookie('auth')){
            cookie('auth', null);
        }
        if(!session('user') && !cookie('auth')) return true;
        else{
            $this->error = '退出登录失败';
            return false;
        }
    }

    //设置7天内自动登陆
    //@param string $uid : user id
    public function setAutoLogin($uid){
        $salt = C('SALT');
        $identifier = md5($salt . md5($uid . $salt));
        $token = md5(uniqid(rand(), TRUE));
        $timeout = time() + 60 * 60 * 24 * 7;
        $res = $this->where(array('id'=>$uid))->save(array('identifier'=>$identifier, 'token'=>$token));
        if(false!==$res){
            cookie('auth', $identifier.':'.$token, $timeout);
        }
    }

    /*
 * 用户密码加密
 * */
    private final function userPassEncrypt($pass){
        return md5(C('SALT').md5($pass));
    }

}