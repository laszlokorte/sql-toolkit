<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Sort {
	private $columnName;

	public function __construct($columnName) {
		$this->columnName = $columnName;
	}
}
