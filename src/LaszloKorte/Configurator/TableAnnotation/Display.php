<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Display implements Annotation {
	public $templateString;

	public function __construct($params) {
		$this->templateString = $params['value'];
	}
}
