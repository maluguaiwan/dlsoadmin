<?php
/**
 * 部件类
 */
namespace app\admin\widget;

use think\Controller;

class Widget extends Controller
{
    public function header(){
        return $this->fetch('widget/header');
    }
    
    public function ajaxTable($data){
        $default = array();
        $default['id']='dt';
        $default['title'] = '';
        $default['color'] = 'blue';
        $default['class'] = 'table-full-width';
        $default['nobox']=0;
        $default['nopageselect']=0;
        $default['report']='';
        $default['isAdvSearch']=0;
        $default['un_default_adv_search']=0;
        $default['adv_search_display']=0;
        $default['adv_search_file']='';
        $default['unchecker']=1;
        $default['table_header']='show';
        $default['advSearchInTable']=0;
        $default['footer']=0;
        $default['column_control']=1;
        $default['table_class']=' table-striped table-hover table-bordered';
        $default['tpl_config']=[];
        $default['url_dynamic']=0;
        $data = array_merge($default, $data);
        if (isset($data['scrolling'])) {
            $data['class'] = str_replace('table-full-width', '', $data);
        }
        if (! isset($data['action'])) {
            $data['action'] = '<a href="' . url('add') . '" class="btn btn-default green pull-right"/><i class="icon-plus"></i> ' . $data['title'] . '</a>';
        }
        
        foreach ($data['columns'] as $key => $value) {
            if(!isset($data['columns'][$key]['defaultVisble'])){
                $data['columns'][$key]['defaultVisble']='visble';
            }
        }
        $this->assign($data);
        return $this->fetch('widget/ajaxtable');
    }
}

