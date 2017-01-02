<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Description {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}
}
