<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Visible implements Annotation {
	public $isVisible;

	public function __construct($params) {
		$this->isVisible = (boolean)$params['value'];
	}
}
