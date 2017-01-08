<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Sort implements Annotation {
	public $columnName;

	public function __construct($params) {
		$this->columnName = $params['value'];
	}
}
