<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Admin;

class Login extends Controller
{
    /**
     * 登录页面
     * @return mixed|string
     */
    public function index(){
        if(session('?admin_id')){
            $this->redirect(url('Index/index'));
        }
        return $this->fetch();
    }
    
    /**
     * 登录
     * @return \think\response\Json
     */
    public function login(){
        if($this->request->isPost()){
            $username = $this->request->post('username','');
            $password = $this->request->post('password','');
            if(!$username){
                return json(['errcode'=>1,'errmsg'=>'用户名不能为空']);
            }
            if(!$password){
                return json(['errcode'=>1,'errmsg'=>'密码不能为空']);
            }
            $admin = Admin::where(['username'=>$username,'password'=>md5($password)])->find();
            if(!$admin){
                return json(['errcode'=>1,'errmsg'=>'用户名或密码不正确']);
            }
            session('admin_id',$admin->id);
            session('username',$admin->username);
            // 查询权限
            // TODO
            
            return json(['errcode'=>0,'errmsg'=>'ok','url'=>url('Index/index')]);
        }
        return json(['errcode'=>1,'errmsg'=>'非法访问']);
    }
    
    /**
     * 退出
     */
    public function logout(){
        session('admin_id',null);
        session('username',null);
        $this->success('退出成功',url('Login/index'));
    }
}

