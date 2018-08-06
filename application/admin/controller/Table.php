<?php
namespace app\admin\controller;

use app\common\model\Admin;

class Table extends AdminBase
{
    use \common\traits\TableTrait;
    public function index()
    {
        $column = [
            'id:数据ID',
            'username:用户名@150px',
            '!password:密码@150px',
            '!status:状态',
            '!last_login_ip:IP地址',
            'last_login_time:登陆时间-formatDate',
            'create_time:创建时间'
        ];
        $this->title = '测试表格';
        $table_setting['title'] = $this->title;
        $table_setting['is_default_search'] = 'show';
        $table_setting['buttons']=1;
        $table_setting['table_header']='hide';
        $table_setting['isAdvSearch']=1;
        $table_setting['nopage'] = 0;
        $table_setting['adv_search_file'] = 'Table/search';
        return $this->_ajaxTable ( $column, url ( 'ajaxdata' ), $table_setting, $this->title, 'Table/index' );
    }
    
    public function ajaxdata(){
        // TODO 高级查询 
        // $table_setting 为nopage 是 LIST_ROWS = -1 查询所有
        if(LIST_ROWS>0){ 
            $list = Admin::order(getDefaultOrder())->limit(FIRST_ROW,LIST_ROWS)->select();
        }else{
            $list = Admin::order(getDefaultOrder())->select();
        }
        
        $count = Admin::count();
        foreach ($list as &$v){
            $v['action_str'] = auth_action(CONTROLLER_NAME.'/edit', 'a', '编辑', ['id' => $v['id']]);
            $v['action_str'] .= auth_action(CONTROLLER_NAME.'/delete', 'box', '删除', ['id' => $v['id']], 'danger', 'xs', 'trash');
        }
        return $this->_ajax_return_data ( $list, $count );
    }
}
