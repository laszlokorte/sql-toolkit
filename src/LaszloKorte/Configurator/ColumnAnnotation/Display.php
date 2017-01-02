<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;


/**
 * @Annotation 
 */
class Display {
	private $templateString;

	public function __construct($templateString) {
		$this->templateString = $templateString;
	}
}
