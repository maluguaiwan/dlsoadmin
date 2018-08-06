<?php
namespace app\common\model;

use think\Model;

class AuthGroupAccess extends Model
{
    public function authGroup()
    {
        return $this->hasOne('AuthGroup', 'id', 'group_id');
    }
    
    public function user()
    {
        return $this->hasOne('Admin', 'id', 'uid');
    }
    
}