<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Visible {
	private $isVisible;

	public function __construct($isVisible) {
		$this->isVisible = $isVisible;
	}
}
