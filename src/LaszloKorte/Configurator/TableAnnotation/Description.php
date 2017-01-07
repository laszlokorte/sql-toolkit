<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Description {
	private $text;

	public function __construct($params) {
		$this->text = (string)$prams['value'];
	}
}
