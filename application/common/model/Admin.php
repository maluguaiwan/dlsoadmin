<?php
namespace app\common\model;

use think\Model;

class Admin extends Model
{
    protected $pk = 'id';
    
    protected $autoWriteTimestamp = true;
}

