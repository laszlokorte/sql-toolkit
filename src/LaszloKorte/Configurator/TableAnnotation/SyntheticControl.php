<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class SyntheticControl implements Annotation {
	public $interfaceName;
	public $columns;
	public $params;

	public function __construct($params) {
		$this->interfaceName = $params['value'];
		$this->columns = $params['columns'];
		$this->params = array_diff_key($params, array_flip(['value','columns']));
	}
}
