<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Priority {
	private $value;

	public function __construct($params) {
		$this->value = $params['value'];
	}
}
