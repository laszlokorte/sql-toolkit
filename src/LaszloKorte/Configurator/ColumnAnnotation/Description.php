<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Description {
	private $text;

	public function __construct($params) {
		$this->text = $params['value'];
	}
}
