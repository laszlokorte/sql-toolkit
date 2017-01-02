<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Aggregatable {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}
}
