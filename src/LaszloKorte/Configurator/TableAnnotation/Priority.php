<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Priority {
	private $value;

	public function __construct($value) {
		$this->value = $value;
	}
}
