<?php

namespace common\db;

use think\db\Query;
use common\utils\Excel;
class Report {
	use \common\traits\TableTrait;
	
	private $column; // pk
	private $dataProcessor;
	private $chunkSize=50;
	private $query;
	private $completeProcessor;
	private $dataCount=0;
	private $data=[];
	private $titles=[];
	private $keys=[];
	
	/**
	 * @return the $completeProcessor
	 */
	public function getCompleteProcessor() {
		return $this->completeProcessor;
	}

	/**
	 * @param field_type $completeProcessor
	 */
	public function setCompleteProcessor($completeProcessor) {
		$this->completeProcessor = $completeProcessor;
	}

	/**
	 * @return Query $query
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * @param Query $query
	 */
	public function setQuery($query) {
		$this->query = $query;
	}

	/**
	 * @return the $column
	 */
	public function getColumn() {
		return $this->column;
	}


	/**
	 * @return the $chunkSize
	 */
	public function getChunkSize() {
		return $this->chunkSize;
	}

	/**
	 * @param field_type $column
	 */
	public function setColumn($column) {
		$this->column = $column;
	}


	/**
	 * @param field_type $chunkSize
	 */
	public function setChunkSize($chunkSize) {
		$this->chunkSize = $chunkSize;
	}

	
	
	/**
	 * @return the $dataProcessor
	 */
	public function getDataProcessor() {
		return $this->dataProcessor;
	}

	/**
	 * @param field_type $dataProcessor
	 */
	public function setDataProcessor($dataProcessor) {
		$this->dataProcessor = $dataProcessor;
	}

	/**
	 * @return the $dataCount
	 */
	public function getDataCount() {
		return $this->dataCount;
	}

	/**
	 * @param number $dataCount
	 */
	public function setDataCount($dataCount) {
		$this->dataCount = $dataCount;
	}
	/**
	 * @param number $dataCount
	 */
	public function incDataCount($dataCount){
		$this->dataCount+=$dataCount;
	}

	private function processColumn($columns){
		// 如果给定的列是一个数组的话。
		$_columns = array ();
		if (is_array ( $columns )) {
			$column_temps=array_values($columns);
			// 遍历
			foreach ( $column_temps as $key => $column ) {
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
				$name='';
				if(stripos($column, ':')!==false){
					list ($name, $column_title) = @explode(':', $column, 2);
				}
				// 如果没有指定title的话，使用列的值
				if (! $column_title) {
					$column_title = $name;
				}
				if(!empty($name)){
				$_columns[$name]=$column_title;
				}
			}
		}
		return empty($_columns)?$columns:$_columns;
	}
	
	/**
	 * 
	 * @param array $columns
	 * @param string $title
	 * @return Ambigous <boolean, number>
	 */
	public function report($columns ,$filename){
		$columns=$this->processColumn($columns);
		try{
			$this->setColumns($columns);
			$this->getQuery()->chunk($this->getChunkSize(), function($dataList){
				flog($this->getQuery()->getLastSql());
				$dataList=call_user_func($this->getDataProcessor(),$dataList);
				return $this->processData($dataList);
			},$this->getColumn());
			
			if($this->getCompleteProcessor()){
				call_user_func($this->getCompleteProcessor(),$this->data,count($this->data));
			}
			Excel::report(array_merge([$this->titles],$this->data), $filename);
		}catch(\Exception $e){
			header_remove('Content-type');
			header_remove('Content-Disposition');
			header_remove('Pragma');
			header_remove('Expires');
			throw $e;
		}
	}
	
	
	/**
	 * @param multitype: $columns
	 */
	private function setColumns($columns) {
		$this->titles=array_values($columns);
		$this->keys = array_keys($columns);
	}
	
	private function processData($dataList){
		foreach ($dataList as $v){
			$row=[];
			foreach ($this->keys as $k){
				$row[]=$v[$k];
			}
			array_push($this->data, $row);
		}
		$count=count($dataList);
		if($count<$this->getChunkSize()){
			return false;
		}
		return true;
	}
}

?>