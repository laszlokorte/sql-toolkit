<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Interface {
	private $name;

	public function __construct($name) {
		$this->name = $name;
	}
}
