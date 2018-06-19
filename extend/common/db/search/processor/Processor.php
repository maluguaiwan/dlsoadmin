<?php

namespace common\db\search\processor;

use common\db\search\Where;
abstract class Processor {
	protected $search;
	protected $where;
	/**
	 * 
	 * @var Where
	 */
	protected $whereInstance;
	private static $instance=null;
	/**
	 * 
	 * @param Where $whereInstance
	 * @param array $search
	 * @param array $where
	 */
	function __construct($whereInstance,$search,&$where) {
		$this->search = $search;
		$this->where=$where;
		$this->whereInstance=$whereInstance;
	}
	public abstract function run($options);
	
	
	protected function isExists($key){
		if(isset($this->search[$key])&&$this->search[$key]){
			return true;
		}
		return false;
	}
	public function filter($value,$filter=null){
		
		if($filter&&is_callable($filter)){
			$value = call_user_func($filter, $value);
		}
		return $value;
	}
}

?>