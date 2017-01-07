<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Title {
	private $singular;
	private $plural;

	public function __construct($title) {
		if(isset($title['value'])) {
			$this->singular = $title['value'];
			$this->plural = NULL;
		} else if(isset($title['singular'])) {
			$this->singular = $title['singular'];
			$this->plural = isset($title['plural']) ? $title['plural'] : null;
		}
	}
}
