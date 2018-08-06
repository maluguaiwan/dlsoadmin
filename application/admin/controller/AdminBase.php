<?php
namespace app\admin\controller;

use think\Controller;
use common\utils\Auth;
use app\common\model\AuthRule;

class AdminBase extends Controller
{
    
    public $admin_id;
    public $title;
    public function initialize(){
        if(!session('?admin_id') || empty(session('admin_id'))){
            // 跳转登录页面
            $this->redirect(url('Login/index'));
        }
        $this->admin_id = session('admin_id');
        $request=$this->request;
        defined('FIRST_ROW')       or define('FIRST_ROW', $request->has('firstRow','post')?$request->post('firstRow') : 0);
        defined('LIST_ROWS')       or define('LIST_ROWS', $request->has('listRows','post')?$request->post('listRows') : 10);
        defined('IS_AJAX')         or define('IS_AJAX', $request->isAjax());
        defined('IS_GET')          or define('IS_GET', $request->isGet());
        defined('IS_POST')         or define('IS_POST', $request->isPost());
        defined('NOW_TIME')        or define('NOW_TIME', time());
        defined('UID')             or define('UID', $this->admin_id);   //设置登陆用户ID常量
        defined('MODULE_NAME')     or define('MODULE_NAME', $request->module());   //全小写
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $request->controller());   //首字母大写
        defined('ACTION_NAME')     or define('ACTION_NAME', $request->action());   //全小写
        
        if(!$this->request->isAjax()){
            $this->assign('username',session('username'));
            $treeMenu = $this->treeMenu();
            $this->assign('treeMenu', $treeMenu);
            // 根据权限获得菜单
            $auth = new Auth();
            if (!$auth->check(CONTROLLER_NAME.'/'.ACTION_NAME, UID)){
                $this->error('对不起,您没有权限访问');
            }
        }
        
    }
    
    private function treeMenu()
    {
        $auth_admin = config('app.AUTH_CONFIG.AUTH_ADMIN');
        $treeMenu = cache('DB_TREE_MENU_'.UID);
        if(!$treeMenu){
            $where = ['ismenu' => 1, 'module' => 'admin'];
            if (!in_array(UID, $auth_admin)){
                $where['status'] = 1;
            }
            $arModel = new AuthRule();
            $lists =  $arModel->where($where)->order('sorts ASC,id ASC')->select();
            $treeClass = new \common\utils\Tree();
            $treeMenu = $treeClass->create($lists);
            //判断导航tree用户使用权限
            foreach($treeMenu as $k=>$val){
                if( authcheck($val['name'], UID) == 'noauth' ){
                    unset($treeMenu[$k]);
                }
            }
            cache('DB_TREE_MENU_'.UID, $treeMenu);
        }
        return $treeMenu;
    }
}
