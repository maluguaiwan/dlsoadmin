<?php
namespace app\admin\controller;

use app\common\model\AuthRule as AuthRuleModel;

class AuthRule extends AdminBase
{
    
    public function initialize()
    {
        parent::initialize();
    }
    
    public function index()
    {
        $model = new AuthRuleModel();
        $dataList = $model->treeList('admin');
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }
    
    protected function cleanCache()
    {
        cache('DB_TREE_AUTHRULE__', null);
        cache('DB_TREE_AUTHRULE_admin_', null);
        cache('DB_TREE_AUTHRULE_admin_1', null);
        cache('DB_TREE_AUTHRULE_admin_0', null);
        cache('DB_TREE_AUTHRULE_member_', null);
        cache('DB_TREE_AUTHRULE_member_1', null);
        cache('DB_TREE_AUTHRULE_member_0', null);
        cache('DB_TREE_AUTHRULE__0', null);
        cache('DB_TREE_AUTHRULE__1', null);
    }
}