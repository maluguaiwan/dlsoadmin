<?php

namespace common\db\search\processor;
/**
 * between
 * @author lpdx111
 *
 */
class BetweenProcessor extends Processor {
	/**
	 * $options ==[start_time,end_time,filter]
	 * @see \common\db\search\processor\Processor::run()
	 */
	public function run($options) {
		$valueKey1=$options[0];
		$valueKey2=$options[1];
		$filter=isset($options[2])?$options[2]:null;
		if($this->isExists($valueKey1)&&$this->isExists($valueKey2)){
			$value1=$this->filter($this->search[$valueKey1],$filter);
			$value2=$this->filter($this->search[$valueKey2],$filter);
			return ['between',[$value1,$value2]];
		}else if($this->isExists($valueKey1)){
			$value1=$this->filter($this->search[$valueKey1],$filter);
			return['egt',$value1];
		}else if($this->isExists($valueKey2)){
			$value2=$this->filter($this->search[$valueKey2],$filter);
			return['elt',$value2];
		}
		return false;
	}
}

?>