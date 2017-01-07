<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Sort {
	private $columnName;

	public function __construct($params) {
		$this->columnName = $params['value'];
	}
}
