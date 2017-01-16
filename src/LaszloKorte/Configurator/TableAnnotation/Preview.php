<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Preview implements Annotation {
	public $urlTemplte;

	public function __construct($params) {
		$this->urlTemplte = $params['value'] ?? $params['url'];
	}
}
