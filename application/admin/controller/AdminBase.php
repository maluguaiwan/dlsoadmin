<?php
namespace app\admin\controller;

use think\Controller;

class AdminBase extends Controller
{
    
    public $admin_id;
    public $title;
    public function initialize(){
        $request=$this->request;
        defined('FIRST_ROW') or define('FIRST_ROW', $request->has('firstRow','post')?$request->post('firstRow') : 0);
        defined('LIST_ROWS') or define('LIST_ROWS', $request->has('listRows','post')?$request->post('listRows') : 10);
        defined('IS_AJAX')   or define('IS_AJAX', $request->isAjax());
        defined('IS_GET')    or define('IS_GET', $request->isGet());
        defined('IS_POST')   or define('IS_POST', $request->isPost());
        defined('NOW_TIME')  or define('NOW_TIME', time());
        if(!session('?admin_id')){
            // 跳转登录页面
            $this->redirect(url('Login/index'));
        }
        $this->admin_id = session('admin_id');
        if(!$this->request->isAjax()){
            $this->assign('username',session('username'));
            
            // 根据权限获得菜单
            
        }
        
    }
}
