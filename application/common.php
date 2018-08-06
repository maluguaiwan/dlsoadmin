<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * @Title: authcheck
 * @Description: todo(权限节点判断)
 * @param string $rule
 * @param int $uid
 * @param string $relation
 * @param string $t
 * @param string $f
 * @return string
 * @throws
 */
function authcheck($rule, $uid, $relation='or', $t='', $f='noauth'){
    $auth = new \common\utils\Auth();
    if( $auth->check($rule, $uid, $type=1, $mode='url',$relation) ){
        $result = $t;
    }else{
        $result = $f;
    }
    return $result;
}

/**
 * @Title: auth_action
 * @Description: todo(操作按钮权限)
 * @param string $rule 权限节点
 * @param string $cationType 按钮样式
 * @param string $info 按钮文字
 * @param string || array  $param 参数
 * @param string $color 颜色
 * @param string $size 大小
 * @param string $icon 图标
 * @return string
 * @throws
 */
function auth_action($rule, $cationType='a', $info='infos', $param=[], $color='primary', $size='xs', $icon='edit'){
    $cationTypes = [
        'a' => "<a class=\"btn btn-".$color." btn-".$size."\" href=\"".url($rule, $param)."\"><i class=\"fa fa-".$icon."\"></i> ".$info."</a>",
        'box' => "<a class=\"btn btn-".$color." btn-".$size." delete\" href=\"javascript:void(0);\" data-url=\"".url($rule,$param)."\" data-title=\"".$info."\" ><i class=\"fa fa-".$icon."\"></i> ".$info."</a>",
        'btn' => "<button type=\"submit\" class=\"btn btn-".$color." btn-".$size." pull-right submits\" data-loading-text=\"&lt;i class='fa fa-spinner fa-spin '&gt;&lt;/i&gt; ".$info."\">".$info."</button>",
    ];
    if( authcheck($rule, UID) != 'noauth' ){
        $result = $cationTypes[$cationType];
    }else{
        $result = '';
    }
    return $result;
}

/**
 * 获得表格默认排序
 * @param string $field
 * @param string $orderType
 * @return string
 */
function getDefaultOrder($field = 'id', $orderType = 'DESC') {
    if (!empty ( $_REQUEST['order_field'] )) {
        return $_REQUEST['order_field'] . ' ' . $_REQUEST['order_type'];
    } else {
        $order = '';
        if (is_array ( $field )) {
            foreach ( $field as $key => $f ) {
                if ($order) {
                    $order .= ',' . $f . ' ';
                    if (is_array ( $orderType )) {
                        $order .= $orderType[$key];
                    } else {
                        $order .= $orderType;
                    }
                } else {
                    $order = $f . ' ';
                    if (is_array ( $orderType )) {
                        $order .= $orderType[$key];
                    } else {
                        $order .= $orderType;
                    }
                }
            }
            return $order;
        } else {
            return $field . ' ' . $orderType;
        }
    }
}

/**
 * @Title: auth_rule_admin
 * @return array
 * @throws
 */
function auth_rule_admin(){
    $authRuleModel = new \app\common\model\AuthRule();
    $list = $authRuleModel->treeList('admin', 1);
    $option = [ '0' => lang('auth_rule_top')];
    if (!empty($list)){
        foreach ($list as $k => $v){
            if ($v->h_layer < 3){
                if ($v->h_layer > 1){
                    $lv = '';
                    for ($i = 1; $i < $v->h_layer; $i++){
                        $lv .= '　　';
                    }
                    $lv .= '├ ';
                }else{
                    $lv = '';
                }
                $option[$v->id] = $lv.$v->title;
            }
        }
    }
    return $option;
}

/**
 * @Title: auth_rule_member
 * @Description: todo(会员前台节点)
 * @return array
 * @throws
 */
function auth_rule_member(){
    $authRuleModel = new \app\common\model\AuthRule();
    $list = $authRuleModel->treeList('member', 1);
    $option = [ '0' => lang('auth_rule_top')];
    if (!empty($list)){
        foreach ($list as $k => $v){
            if ($v->h_layer < 3){
                if ($v->h_layer > 1){
                    $lv = '';
                    for ($i = 1; $i < $v->h_layer; $i++){
                        $lv .= '　　';
                    }
                    $lv .= '├ ';
                }else{
                    $lv = '';
                }
                $option[$v->id] = $lv.$v->title;
            }
        }
    }
    return $option;
}


/**
 * @Title: auth_rule
 * @Description: todo(权限节点)
 * @return array
 * @throws
 */
function auth_rule(){
    $authRuleModel = new \app\common\model\AuthRule();
    $list = $authRuleModel->treeList('', 1);
    $option = [];
    if (!empty($list)){
        foreach ($list as $k => $v){
            $option[$v->id] = [$v->level, $v->title];
        }
    }
    return $option;
}
