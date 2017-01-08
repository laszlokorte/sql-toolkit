<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Title implements Annotation {
	public $singular;
	public $plural;

	public function __construct($params) {
		if(isset($params['value'])) {
			$this->singular = (string)$params['value'];
			$this->plural = NULL;
		} else if(isset($params['singular'])) {
			$this->singular = (string)$params['singular'];
			$this->plural = isset($params['plural']) ? 
				(string)$params['plural'] : null;
		}
	}
}
