<?php
namespace app\common\model;

use think\Model;

class AuthGroup extends Model
{
    private $auth_group = [
        'title' => '用户组别',
        'status' => '1',
        'data' => [
            'admin' => '管理员角色',
            'member' => '会员角色',
        ],
    ];
    public function getModuleTurnAttr($value, $data)
    {
        $list = $this->auth_group;
        $turnArr = $list['data'];
        return $turnArr[$data['module']];
    }
    
    public function setRulesAttr($value)
    {
        if (!empty($value)){
            return $value = implode(',', array_filter($value));
        }else{
            return $value = '';
        }
    }
}