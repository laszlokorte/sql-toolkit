<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Id implements Annotation {
	public $id;

	public function __construct($params) {
		$this->id = $params['value'];
	}
}
