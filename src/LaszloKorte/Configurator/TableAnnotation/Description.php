<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Description {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}
}
