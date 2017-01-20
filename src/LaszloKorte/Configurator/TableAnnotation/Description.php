<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Description implements Annotation {
	public $text;

	public function __construct($params) {
		$this->text = (string)$params['value'];
	}
}
