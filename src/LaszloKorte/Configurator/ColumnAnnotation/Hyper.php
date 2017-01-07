<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;


/**
 * @Annotation 
 */
class Hyper {

	private $type;

	public function __construct($params) {
		$this->type = $params['value'];
	}
}
