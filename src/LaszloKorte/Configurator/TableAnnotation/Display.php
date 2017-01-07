<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Display {
	private $templateString;

	public function __construct($params) {
		$this->templateString = $params['value'];
	}
}
