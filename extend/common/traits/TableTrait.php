<?php
namespace common\traits;
use common\db\Report;
use common\utils\Excel;
const TABLE_FILE='common:table';
const AJAX_TABLE_FILE='common:ajaxtable';
trait TableTrait
{
    private function _table_column_processor($column_array=null){
        $_columns = [];
        $setting=[];
        foreach ($column_array as $key => $column) {
            $_columns[$key] = array();
            $classes = array();
            if (strpos($column, '!') === 0) {
                $column = substr($column, 1);
                $setting['noSort'][] = $key;
            }
            if (strpos($column, '^') === 0) {
                $column = substr($column, 1);
                $_columns[$key]['defaultVisble'] = 'hidden';
            }
            if (strpos($column, '[') === 0 && strpos($column, ']') > 0) {
                $fun = substr($column, 1, strpos($column, ']') - 1);
                $classes[] = 'column-group';
                $classes[] = 'column-group-' . $fun;
                $table_setting['column_group'] = 1;
                $column = substr($column, strpos($column, ']') + 1);
            }
            $width=0;
            if(stripos($column, '@')!==false){
                list ($column, $width) = explode('@', $column, 2);
            }
            $class='';
            if(stripos($column, '.')!==false){
                list ($column, $class) = explode('.', $column, 2);
            }
            $fn='';
            if(stripos($column, '-')!==false){
                list ($column, $fn) = @explode('-', $column, 2);
            }
            if(stripos($column, ':')!==false){
                list ($name, $title) = @explode(':', $column, 2);
            }
    
            if ($class)
                $classes[] = $class;
            if ($key > 5)
                $classes[] = 'hidden-phone';
            if ($name == 'action_str')
                $classes[] = ' btn-operate';
            if (! $title)
                $title = $name;
            if ($fn)
                $_columns[$key]['fnRender'] = $fn;
            $_columns[$key]['mData'] = $name;
            $_columns[$key]['sTitle'] = lang($title);
            $_columns[$key]['sClass'] = implode(' ', $classes);
            if ($width) {
                $_columns[$key]['sWidth'] = $width;
            }
        }
        $setting['columns'] = $_columns;
        
        $setting['nosort'] = isset($setting['noSort'])?implode(',', $setting['noSort']):'';
        $setting['bootstarp']=3;
        $setting['nobox']=1;
        $setting['column_control']=0;
        $table_setting['table_header']='hide';
        return $setting;
    }
    
    
    /**
     *
     * @param string $column_array
     * @param string $table_setting
     * is_clear_data
     * @param string $tpl
     * @param string $right_btn
     * @param string $is_clear_button
     */
    protected function _table($column_array = null, $table_setting = null, $tpl = '', $right_btn = null,$is_clear_button = false,$returnData=false) {
        if(isset($table_setting['ajaxURL'])){
            return $this->_ajaxTable($column_array,$table_setting['ajaxURL'] ,$table_setting,$right_btn,$tpl);
        }
        if (is_array($column_array)) {
            $column_array = array_values($column_array);
            $setting=$this->_table_column_processor($column_array);
            if ($table_setting) {
                $setting = array_merge($setting, $table_setting);
            }
        }else{
            $setting=$table_setting;
        }
        $fn_arr=[];
        foreach ($setting['columns'] as $key => $value) {
            if (isset($value['fnRender'])) {
                $fn_arr[] = array(
                    'fn' => $value['fnRender'],
                    'key' => $value['mData']
                );
            }
        }
        if ($fn_arr) {
            if(isset($setting['data'])&&is_array($setting['data'])){
                foreach ($setting['data'] as $key => $value) {
                    foreach ($fn_arr as $k => $v) {
                        $fn = $v['fn'];
                        if (method_exists($this, $fn)) {
                            $setting['data'][$key][$v['key']] = $this->$fn($value[$v['key']], $value);
                            foreach ($setting['columns'] as $y => $s) {
                                if ($s['mData'] == $v['key']) {
                                    unset($setting['columns'][$y]['fnRender']);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        // aa($setting);
        $this->assign('setting', $setting);
    
        if (! $right_btn) {
            $title = $this->_title;
            $right_btn = $title . '<a href="' . url(request()->module() . '/' . request()->controller() . '/add') . '" class="btn green pull-right" style="margin-left:10px;"/><i class="icon-plus"></i> 添加' . $title . '</a>';
        }
    
        $this->_head_btn = $right_btn;
        $this->assign('_head_btn',$right_btn);
        if (! $tpl) {
            $tpl = TABLE_FILE;
        }
        return $this->fetch($tpl);
    }

    protected function _ajaxTable(array $column_array,$url,$table_setting=null,$right_btn=null,$tpl=null){
        $column_array = array_values($column_array);
        $find_action = false;
        foreach ($column_array as $key => $v) {
            $find_action = strpos($v, 'action_str');
            if ($find_action !==false) {
                $find_action = true;
                break;
            }
        }
        if (!$find_action&&!isset($table_setting['noaction'])) {
            $column_array[] = '!action_str:操作';
        }
        $setting=$this->_table_column_processor($column_array);
        if ($table_setting) {
            $setting = array_merge($setting, $table_setting);
        }
        if(!isset($table_setting['config'])){
        	$table_setting['config'] = ['cache_time'=>0];
        }
        if($url){
            $setting['ajaxURL']=$url;
        }
        $this->assign('setting', $setting);
        if (! $right_btn) {
            $title = $this->_title;
            $right_btn = $title . '<a href="' . url( request()->module() . '/' . request()->controller() . '/add') . '" class="btn green pull-right" style="margin-left:10px;"/><i class="icon-plus"></i> 添加' . $title . '</a>';
        }
        $this->assign('_head_btn',$right_btn);
        if (!$tpl) {
            $tpl = AJAX_TABLE_FILE;
        }
        $this->assign('ajaxURL',$setting['ajaxURL']);
        if(isset($table_setting['tpl_config'])){
        	return $this->fetch($tpl,[],$table_setting['config'],$table_setting['tpl_config']);
        }
        return $this->fetch($tpl);
    }
    
    /**
     * 
     * @param array $columns 列设置
     * @param array||  Report $data
     * @param string $title
     */
    protected function _report($columns, $data,$filename='导出数据') {
    	// 如果给定的列是一个数组的话。
    	$_columns = array ();
    	if (is_array ( $columns )) {
    		$columns=array_values($columns);
    		// 遍历
    		foreach ( $columns as $key => $column ) {
    			if (strpos ( $column, '!' ) === 0) {
    				$column = substr ( $column, 1 );
    			}
	            if(stripos($column, '@')!==false){
	                list ($column) = explode('@', $column, 2);
	            }
	            if(stripos($column, '.')!==false){
	                list ($column) = explode('.', $column, 2);
	            }
	            if(stripos($column, '-')!==false){
	                list ($column) = @explode('-', $column, 2);
	            }
	            $column_title='';
	            if(stripos($column, ':')!==false){
	                list ($name, $column_title) = @explode(':', $column, 2);
	            }
    			// 如果没有指定title的话，使用列的值
    			if (! $column_title) {
    				$column_title = $name;
    			}
    			array_push($_columns, [
    				'key'=>$name,
    				'title'=>$column_title
    			]);
    		}
    	}

    	$columns=array();
    	foreach ($_columns as $k=>$column){
    		$columns[$column['key']]=$column['title'];
    	}
	    
	    try{
		    if(is_object($data) && $data instanceof Report){
		    	$report=$data;
		    	$report->setColumns($columns);
		    	return $report->execute($filename);
		    }else{
		    	$titles=array_values($columns);
		    	$keys = array_keys($columns);
			    foreach ($data as $k=>$v){
			    	$row=[];
			    	foreach ($keys as $k){
			    		$row[]=$v[$k];
			    	}
			    	$data[$k]=$row;
			    }
			    Excel::report(array_merge($titles,$data), $filename);
			    return count($data);
		    }
	    }catch(\Exception $e){
	    	header_remove('Content-type');
	    	header_remove('Content-Disposition');
	    	header_remove('Pragma');
	    	header_remove('Expires');
	    	throw $e;
	    }
    }
    
    public function _ajax_return_data($data,$total_count=0){
        $mdata=array( "iTotalDisplayRecords"=> $total_count );
        $mdata['aaData']=$data?$data:[];
        
        foreach ($mdata['aaData'] as $key=>$v){
            if(isset($v['id'])){
                $v['DT_RowId']=$v['id'];
            }
            $mdata['aaData'][$key]=$v;
        }
        return $mdata;
    }
}

?>