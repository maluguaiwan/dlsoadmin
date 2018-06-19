<?php

namespace common\db\search\processor;

/**
 * function
 * 
 * @author lpdx111
 *        
 */
class FunctionProcessor extends Processor {
	/**
	 * $options =[value,function(){}]
	 * 
	 * @see \common\db\search\processor\Processor::run()
	 */
	public function run($options) {
		$valueKey = $options[0];
		if (is_string ( $valueKey )) {  // 单独的string 值
			$function = $options[1];
			if ($this->isExists ( $valueKey )) {
				$value = $this->search[$valueKey];
				if (is_callable ( $function )) {
					$result = call_user_func_array ( $function, [ $value ] );
					if ($result !== false) {
						$buildOptions = null;
						if (is_array ( $result )) {
							$buildOptions = $result;
						} else {
							$buildOptions = [ 'eq',$result ];
						}
						return $buildOptions;
					}
				}
			}
		} else if (is_array ( $valueKey )) {  // 多个值
			$values = [ ];
			foreach ( $valueKey as $key ) {
				if ($this->isExists ( $key )) {
					$value = $this->search[$key];
					$values[$key] = $value;
				}
			}
			if (! empty ( $values )) {
				if (is_callable ( $function )) {
					$result = call_user_func_array ( $function, [$values ] );
					if ($result !== false) {
						$buildOptions = null;
						if (is_array ( $result )) {
							$buildOptions = $result;
						} else {
							$buildOptions = [ 'eq',$result ];
						}
						return $buildOptions;
					}
				}
			}
		}
		return false;
	}
}

?>