<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Visible {
	private $isVisible;

	public function __construct($params) {
		$this->isVisible = (boolean)$params['value'];
	}
}
