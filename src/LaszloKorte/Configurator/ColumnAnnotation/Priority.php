<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Priority implements Annotation {
	public $value;

	public function __construct($params) {
		$this->value = $params['value'];
	}
}
