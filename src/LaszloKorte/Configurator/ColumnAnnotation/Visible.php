<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Visible implements Annotation {
	public $isVisible;

	public function __construct($params) {
		$this->isVisible = $params['value'];
	}
}
