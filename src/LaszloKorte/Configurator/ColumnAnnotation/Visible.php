<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Visible {
	private $isVisible;

	public function __construct($isVisible) {
		$this->isVisible = $isVisible;
	}
}
