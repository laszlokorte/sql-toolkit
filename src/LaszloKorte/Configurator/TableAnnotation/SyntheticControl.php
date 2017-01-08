<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class SyntheticControl implements Annotation {
	public $interfaceName;
	public $params;

	public function __construct($params) {
		$this->interfaceName = $params['value'];
		$this->params = array_diff_key($params, array_flip(['value']));
	}
}
