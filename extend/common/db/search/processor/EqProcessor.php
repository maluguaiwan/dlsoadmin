<?php

namespace common\db\search\processor;

/**
 * 相等
 *
 * @author lpdx111
 *        
 */
class EqProcessor extends Processor {
	/**
	 * options=[value,filter]
	 * @see \common\db\search\processor\Processor::run()
	 */
	public function run( $options) {
		$valueKey=$options[0];
		$filter=isset($options[1])?$options[1]:null;
		if ($this->isExists ( $valueKey )) {
			return  $this->filter($this->search [$valueKey],$filter);
		}
		return false;
	}
}

?>