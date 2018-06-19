<?php

namespace common\db\search\processor;

class InProcessor extends Processor {
	/**
	 * $options=[values,filter]
	 * @see \common\db\search\processor\Processor::run()
	 */
	public function run($options) {
		$valueKey=$options[0];
		$filter=isset($options[1])?$options[1]:null;
		if($this->isExists($valueKey)){
			return ['in',$this->filter($this->search[$valueKey],$filter)];
		}
		return false;
		
	}
}

?>