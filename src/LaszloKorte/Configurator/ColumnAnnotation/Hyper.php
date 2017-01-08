<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;


/**
 * @Annotation 
 */
class Hyper implements Annotation {

	public $type;

	public function __construct($params) {
		$this->type = $params['value'];
	}
}
