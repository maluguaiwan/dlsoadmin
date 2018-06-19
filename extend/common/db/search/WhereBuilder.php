<?php

namespace common\db\search;

use think\Loader;
use common\db\search\processor\Processor;

class WhereBuilder {
	private $search = [ ];
	private $where = [ ];
	private $whereProcessors = [ ];
	/**
	 *
	 * @param string $query        	
	 */
	public static function instance($search = 'advSearch',$where=[]) {
		$builder = new WhereBuilder ();
		if(is_string($search)){
			$query = array ();
			parse_str ( request()->request ($search), $query );
			$builder->search = $query;
		}else if(is_array($search)){
			$builder->search=$search;
		}else if(is_object($search)&&$search instanceof Base64Search){
			$builder->search=$search->decode();
		}
		$builder->where=$where;
		return $builder;
	}
	public function getSearch($field=null){
		if($field){
			return isset($this->search[$field])?$this->search[$field]:false;		
		}else{
			return $this->search;
		}
	}
	/**
	 * 生成Where
	 *
	 * @param unknown $options        	
	 */
	public function build($options) {
		$buildOptions = [ ];
		foreach ( $options as $k => $v ) {
			if (is_string ( $k )) {
				if (is_string ( $v )) {
					$buildOptions [$k] = [ 
						'eq',
						$v ];
				} else if (is_array ( $v )) {
					$buildOptions [$k] = $v;
				}
			} else {
				$buildOptions [$v] = [ 
					'eq',
					$v ];
			}
		}
		foreach ( $buildOptions as $k => $v ) {
			$type = array_shift ( $v );
			$processor = $this->getProcessor ( $type );
			$value=$processor->run ($v);
			if($value!==false){
				$this->where[$k]=$value;
			}
		}
		return $this->where;
	}
	/**
	 *
	 * @param string $type        	
	 * @return Processor
	 */
	public function getProcessor($type) {
		if (! isset ( $this->whereProcessors [$type] )) {
			$class = "common\\db\\search\\processor\\" . Loader::parseName ( $type, 1 ) . 'Processor';
			$this->whereProcessors [$type] = new $class ($this, $this->search, $this->where );
		}
		return $this->whereProcessors [$type];
	}
}

?>