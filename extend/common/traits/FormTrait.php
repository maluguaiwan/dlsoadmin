<?php
namespace common\traits;
const FORM_FILE='common@Common:form';
const FORM_VIEW_FILE='common@Common:form_view';
trait FormTrait {
	
    
	/**
	 * 创建add
	 *
	 * @param $tpl 输出的模板
	 * @param $return 是否返回数据
	 */
	protected function _add($tpl = '', $return = false, $right_btn = null, $data = null, $formSet = 'formSet')
	{
		$title = $this->_title;
		if (! $right_btn) {
			$right_btn = $title . '<a href="javascript:history.back()" class="btn pull-right"/><i class="m-icon-swapleft"></i> 返回</a>';
		}
		$this->_head_btn = $right_btn;
		$this->assign('_head_btn',$this->_head_btn);
		$form_set = $this->$formSet($data);
		if (! isset($form_set['title'])) {
			$form_set['title'] = '添加' . $title;
		}
		if (! isset($form_set['color'])) {
			$form_set['color'] = 'green';
		}
		$this->form_set=$form_set;
		$this->assign('form_set',$form_set);
		if ($return) {
			return $this->form_set;
		}
		$tpl = $tpl ? $tpl : FORM_FILE;
		return $this->fetch($tpl);
	}
	
	protected function _view($tpl = '', $return = false, $table_name = "", $right_btn = NULL, $db = null, $data = null)
	{
		$id = $this->request->param('id');
		if (! $id) {
			$this->error('空ID');
		}
		if ($table_name === null) {
			$re = $data;
		} else {
			$m = db($table_name?: request()->controller());
			$re = $m->find($id);
			if ($data) {
				$re = array_merge($re, $data);
			}
		}
		$this->id = $id;
		if (! $right_btn) {
			$title = $this->_title;
			$right_btn = $title . '<a href="javascript:history.back()" class="btn pull-right"/><i class="m-icon-swapleft"></i> 返回</a>';
		}
		$this->_head_btn = $right_btn;
		if ($re) {
			$form_set = $this->formViewSet($re);
			if (! $form_set['title']) {
				$form_set['title'] = '查看' . $title;
			}
			if (! $form_set['color']) {
				$form_set['color'] = 'purple';
			}
			$this->form_set = $form_set;
			$this->assign('form_set',$form_set);
		}
		if ($return) {
			return $this->form_set;
		}
		$tpl = $tpl ? $tpl :FORM_VIEW_FILE;
		return $this->fetch($tpl);
	}
	
	/**
	 * 创建edit
	 *
	 * @param $tpl 输出的模板
	 * @param $return 是否返回数据
	 */
	protected function _edit($tpl = '', $return = false, $table_name = '', $right_btn = NULL, $db = null, $data = null, $formSet = 'formSet')
	{
		$id=0;
		if($this->request->has('id')){
			$id=$this->request->param('id');
		}else if($this->request->has('id','request')){
			$id=$this->request->request('id');
		}
		if (!$id ) {
			$this->error('系统错误：ID不存在');
		}
		$m = db($table_name?: request()->controller());
		$re = $m->find($id);
		$this->id = $id;
		$title = $this->_title;
		if (! $right_btn) {
			$right_btn = $title . '<a href="javascript:history.back()" class="btn pull-right"/><i class="m-icon-swapleft"></i> 返回</a>';
		}
	
		$this->_head_btn = $right_btn;
		$this->assign('_head_btn', $this->_head_btn);
		if ($re) {
			if ($data) {
				$re = array_merge($re, $data);
			}
			$form_set = $this->$formSet($re);
			if (! isset($form_set['title'])) {
				$form_set['title'] = '编辑' . $title;
			}
			if (! isset($form_set['color'])) {
				$form_set['color'] = 'purple';
			}
			$this->form_set = $form_set;
			$this->assign('form_set',$form_set);
		}
		$this->assign('vo', $re);
		if ($return) {
			return $this->form_set;
		}
		$tpl = $tpl ? $tpl : FORM_FILE;
		return $this->fetch($tpl);
	}
	
	private function __level_sort__(&$data, $id, $newdata, $parentid = 'parent_id', $sort = 'sort', $level = 0)
	{
		$list = $newdata[$id];
		if ($sort) {
			foreach ($list as $k => $v) {
				$list[$k]['___sort___name'] = $sort;
			}
			uasort($list, function ($a, $b) {
				$sort = $a['___sort___name'];
				$r = $a[sort] - $b[$sort];
				if ($r == 0) {
					return $a['id'] - $b['id'];
				}
				return $r;
			});
		}
	
		$nameappend = '';
		if ($level) {
			$nameappend = '';
			for ($i = 0; $i < $level; $i ++) {
				$nameappend .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			$nameappend .= '┗━';
		}
	
		$level ++;
		foreach ($list as $k => $v) {
			$v['name'] = $nameappend . $v['name'];
			$data[] = $v;
			if (array_key_exists($v['id'], $newdata)) {
				$this->__level_sort__($data, $v['id'], $newdata, $parentid, $sort, $level);
			}
		}
	}
	
	protected function _level_sort($data, $parentid = 'parent_id', $sort = 'sort')
	{
		$newdata = array();
		foreach ($data as $k => $v) {
			$newdata[$v[$parentid]][$v['id']] = $v;
		}
	
		$cdata = array();
		$this->__level_sort__($cdata, 0, $newdata, $parentid, $sort);
		return $cdata;
	}
	protected function _ajaxSave($table_name,$default=NULL){
		$m = db($table_name?:request()->controller());
		$data=[
			'cid'=>session('cid'),
		];
		if ($default) {
			foreach ($default as $key => $value) {
				$data[$key] = $value;
			}
		}
		$data=array_merge($data,request()->post());
		if ($this->request->has('id','post') && !empty($this->request->post('id'))) {
			$m->update($data);
			$id = $this->request->post('id');
		} else {
			unset($data['id']);
			$id = $m->insertGetId($data);
		}
		return ['status'=>1,'id'=>$id];
	}
	protected function _save($table_name = '', $default = NULL, $db = null, $callback = null, $url = null, $params = array())
	{
		$m = db($table_name?:request()->controller());
		if ($db) {
			$m->db($db);
		}
		$data=[
		'cid'=>session('cid'),
		'uid'=>session('uid'),
		];
		if ($default) {
			foreach ($default as $key => $value) {
				$data[$key] = $value;
			}
		}
		$data=array_merge($data,$_POST);
		if ($this->request->has('id','post') && !empty($this->request->post('id'))) {
			$m->update($data);
			$id = $this->request->post('id');
		} else {
			unset($data['id']);
			$id = $m->insertGetId($data);
		}
		if ($id) {
			if ($callback) {
				$this->$callback($m, $id);
			}
			if ($url) {
				$this->redirect($url, $params);
			} else {
				$this->redirect(request()->module() . '/' . request()->controller().'/index');
			}
		} else {
			$this->error('添加失败!');
		}
	}
	
	/**
	 * 给编辑时的表单插入原始值
	 *
	 * @param $form_set 生成表单的设计
	 * @param $data 数据
	 * @param $noset 不用赋值的部件
	 * @return $form_set
	 */
	protected function insertDefalutData($form_set, $data, $noset = 'password')
	{
		$noset = explode(',', $noset);
		foreach ($form_set['content'] as $key => $value) {
			if ($value[0] == 'DateRangepicker') {
				foreach ($data as $k => $v) {
					if ($k == $value[1]['starttime']) {
						if ($v) {
							$form_set['content'][$key][1]['starttimevalue'] = is_numeric($v) ? date('Y-m-d', $v) : $v;
						}
					} else
						if ($k == $value[1]['endtime']) {
							if ($v) {
								$form_set['content'][$key][1]['endtimevalue'] = is_numeric($v) ? date('Y-m-d', $v) : $v;
							}
						}
				}
			} else
				if (in_array($value[0], array(
						'DoubInput',
						'DoubDatepicker'
				))) {
					foreach (array(
							'name1' => 'value1',
							'name2' => 'value2'
					) as $k => $v) {
						if ($value[1][$k] && ! in_array($value[1][$k], $noset)) {
							if (isset($data[$value[1][$k]])) {
								$form_set['content'][$key][1][$v] = $data[$value[1][$k]];
							}
						}
					}
				} else {
	
					if (isset($value[1]['name']) && ! in_array($value[1]['name'], $noset)) {
						if (isset($data[$value[1]['name']])) {
							$form_set['content'][$key][1]['value'] = $data[$value[1]['name']];
						}
					}
				}
		}
		return $form_set;
	}
	/**
	 * 创建del
	 */
	protected function _del($table_name = '', $where = null, $url = null, $url_params = array())
	{
	    $id=0;
	    if($this->request->has('id')){
	        $id=$this->request->param('id');
	    }else if($this->request->has('id','request')){
	        $id=$this->request->request('id');
	    }
	    if (!$id ) {
	        $this->error();
	    }
	    $m = db($table_name?:request()->controller());
	    if (! $where) {
	        $where = getDefaultWhere();
	        $where['id'] = $id;
	    }
	    $id = $m->where($where)->delete();
	    if ($id) {
	        if ($url) {
	            $this->redirect($url, $url_params);
	        } else {
	            $this->redirect(request()->module().'/' . request()->controller().'/index');
	        }
	    } else {
	        $this->error('删除失败!' . $m->getDbError() . $m->_sql());
	    }
	}
	public function add(){
	    $this->_add();
	}
	public function edit(){
	    $this->_edit();
	}
	//@todo 删除权限
	public function del(){
	    $this->_del();
	}
	public function save(){
	    $this->_save();
	}
	
	
	
	/**
	 * 创建select的option数组
	 *
	 * @param $table 表名/M()
	 * @param $value option的名称的字段名
	 * @param $key option的value的字段名
	 * @return 数组 boolean
	 */
	protected function makeSelectArray($table, $name, $key = 'id', $sort = null, $empty = false) {
	    if (is_string ( $table )) {
	        $m = db ( $table );
	        $where = getDefaultWhere ();
	        $m->where ( $where );
	    } else {
	        $m = $table;
	    }
	    if (! $sort) {
	        $sort = $key;
	    }
	    $re = $m->field ( $key . ' as value,' . $name . ' as title' )->order ( $sort )->select ();
	    if ($re) {
	        if ($empty) {
	            $arr = array (
	                array (
	                    'value' => 0,
	                    'title' => '无'
	                )
	            );
	            $re = array_merge ( $arr, $re );
	        }
	        return $re;
	    } else {
	        if ($empty) {
	            $arr = array (
	                array (
	                    'value' => 0,
	                    'title' => '无'
	                )
	            );
	            return $arr;
	        }
	    }
	}
}

?>