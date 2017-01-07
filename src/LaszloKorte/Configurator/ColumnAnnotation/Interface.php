<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Interface {
	private $name;
	private $params;

	public function __construct($params) {
		$this->name = $params['value'];
		$this->params = $params;
	}
}
