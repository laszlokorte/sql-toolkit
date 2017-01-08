<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Description implements Annotation {
	public $text;

	public function __construct($params) {
		$this->text = $params['value'];
	}
}
